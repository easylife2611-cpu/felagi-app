<?php

namespace App\Services\AI;

use App\Models\Need;
use App\Models\Offer;

/**
 * AI-11 — Contradiction detection.
 *
 * AI_Evaluation_Contract.md: contradictions between a Need's stated
 * requirements and an Offer's claims must be surfaced as advisory
 * findings, never auto-rejected. The AI never decides; the requester
 * decides. This detector adds deterministic, non-AI pre-checks that
 * are attached to the comparison result for transparency.
 *
 * Detection categories:
 *   - BUDGET_ABOVE_MAX:   offered_price > need.budget_max
 *   - BUDGET_BELOW_MIN:   offered_price < need.budget_min
 *   - CURRENCY_MISMATCH:  offer.currency !== need.currency
 *   - DELIVERY_UNSPECIFIED: delivery_time_text is empty
 *   - PRICE_UNSPECIFIED:  offered_price is null or <= 0
 *   - AVAILABILITY_UNSPECIFIED: availability_text is empty
 *
 * These are deterministic; no AI call. The detector NEVER blocks a
 * comparison — it only annotates. Advisory only, per contract C04.
 */
class ContradictionDetector
{
    public const TYPE_BUDGET_ABOVE_MAX      = 'BUDGET_ABOVE_MAX';
    public const TYPE_BUDGET_BELOW_MIN      = 'BUDGET_BELOW_MIN';
    public const TYPE_CURRENCY_MISMATCH     = 'CURRENCY_MISMATCH';
    public const TYPE_DELIVERY_UNSPECIFIED  = 'DELIVERY_UNSPECIFIED';
    public const TYPE_PRICE_UNSPECIFIED     = 'PRICE_UNSPECIFIED';
    public const TYPE_AVAILABILITY_UNSPECIFIED = 'AVAILABILITY_UNSPECIFIED';

    /**
     * @return array<int,array{offer_id:string,offer_index:int,type:string,severity:string,message:string}>
     */
    public function detect(Need $need, iterable $offers): array
    {
        $findings = [];
        $needMax  = $need->budget_max;
        $needMin  = $need->budget_min;
        $needCur  = $need->currency ?? 'ETB';

        $i = 0;
        foreach ($offers as $offer) {
            $price = $offer->offered_price;
            $offerCur = $offer->currency ?? $needCur;

            if ($price === null || (float) $price <= 0) {
                $findings[] = $this->finding($offer, $i,
                    self::TYPE_PRICE_UNSPECIFIED, 'HIGH',
                    'Offer does not state a price.');
            } else {
                if ($needMax !== null && (float) $price > (float) $needMax) {
                    $findings[] = $this->finding($offer, $i,
                        self::TYPE_BUDGET_ABOVE_MAX, 'MEDIUM',
                        "Offer price {$price} {$offerCur} exceeds stated budget max {$needMax} {$needCur}.");
                }
                if ($needMin !== null && (float) $price < (float) $needMin) {
                    $findings[] = $this->finding($offer, $i,
                        self::TYPE_BUDGET_BELOW_MIN, 'LOW',
                        "Offer price {$price} {$offerCur} is below stated budget min {$needMin} {$needCur}.");
                }
            }

            if ($offerCur !== $needCur) {
                $findings[] = $this->finding($offer, $i,
                    self::TYPE_CURRENCY_MISMATCH, 'HIGH',
                    "Offer currency {$offerCur} differs from Need currency {$needCur}.");
            }

            if (empty(trim((string) $offer->delivery_time_text))) {
                $findings[] = $this->finding($offer, $i,
                    self::TYPE_DELIVERY_UNSPECIFIED, 'MEDIUM',
                    'Offer does not state a delivery time.');
            }

            if (empty(trim((string) $offer->availability_text))) {
                $findings[] = $this->finding($offer, $i,
                    self::TYPE_AVAILABILITY_UNSPECIFIED, 'LOW',
                    'Offer does not state availability.');
            }

            $i++;
        }

        return $findings;
    }

    private function finding(Offer $offer, int $index, string $type, string $severity, string $message): array
    {
        return [
            'offer_id'    => $offer->id,
            'offer_index' => $index,
            'type'        => $type,
            'severity'    => $severity,
            'message'     => $message,
        ];
    }

    /**
     * Summarise findings for a comparison record.
     *
     * @return array{total:int,by_severity:array<string,int>,by_offer:array<string,int>}
     */
    public function summarise(array $findings): array
    {
        $bySev = ['HIGH' => 0, 'MEDIUM' => 0, 'LOW' => 0];
        $byOffer = [];
        foreach ($findings as $f) {
            $bySev[$f['severity']] = ($bySev[$f['severity']] ?? 0) + 1;
            $byOffer[$f['offer_id']] = ($byOffer[$f['offer_id']] ?? 0) + 1;
        }
        return [
            'total'        => count($findings),
            'by_severity'  => $bySev,
            'by_offer'     => $byOffer,
        ];
    }
}
