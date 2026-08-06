<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Fase 5 – Cálculo de Madurez y Riesgo
 *
 * MODELO DE RESPUESTA POR PREGUNTA:
 *   Sí (yes)      → cumple el control
 *   No (no)       → no cumple
 *   No aplica (na)→ excluida del cálculo
 *
 * NIVEL DE MADUREZ POR CONTROL (asignado manualmente por el auditor, escala 0-5):
 *   Se almacena en audit_control_maturity.maturity_level
 *
 * NIVEL DE CUMPLIMIENTO POR CONTROL:
 *   compliance = preguntas_yes / (preguntas_yes + preguntas_no)  [excluye na]
 *
 * MADUREZ PONDERADA POR DOMINIO:
 *   maturity_domain = SUM(maturity_control * weight) / SUM(weight)
 *
 * MADUREZ GLOBAL:
 *   maturity_global = SUM(maturity_domain) / COUNT(domains)
 *
 * EXPOSICIÓN AL RIESGO POR DIMENSIÓN (C, I, D):
 *   gap_control = 5 - maturity_control
 *   risk_C = SUM(gap * confidentiality * weight) / SUM(confidentiality * weight * 5) * 100
 *   risk_I = SUM(gap * integrity     * weight) / SUM(integrity     * weight * 5) * 100
 *   risk_D = SUM(gap * availability  * weight) / SUM(availability  * weight * 5) * 100
 *
 * ÍNDICE GENERAL DE RIESGO:
 *   risk_global = (risk_C + risk_I + risk_D) / 3
 */
final class MaturityCalculator
{
    /**
     * @param array $controlMaturities  Filas de audit_control_maturity con control metadata
     *                                  Cada fila: control_id, maturity_level, weight,
     *                                             confidentiality, integrity, availability, domain_id
     * @param array $responses          Filas de responses con question metadata
     *                                  Cada fila: question_id, control_id, answer (yes/no/na)
     * @return array{
     *   maturity_score: float,
     *   risk_score: float,
     *   risk_c: float,
     *   risk_i: float,
     *   risk_d: float,
     *   by_domain: array,
     *   by_control: array
     * }
     */
    public static function calculate(array $controlMaturities, array $responses): array
    {
        // Agrupar respuestas por control para calcular cumplimiento
        $complianceByControl = [];
        foreach ($responses as $r) {
            $cid = (int) $r['control_id'];
            if (! isset($complianceByControl[$cid])) {
                $complianceByControl[$cid] = ['yes' => 0, 'no' => 0];
            }
            if ($r['answer'] === 'yes') {
                $complianceByControl[$cid]['yes']++;
            } elseif ($r['answer'] === 'no') {
                $complianceByControl[$cid]['no']++;
            }
            // 'na' se ignora
        }

        $byControl = [];
        $byDomain  = [];

        // Numeradores y denominadores para riesgo por dimensión
        $riskNumC = 0.0; $riskDenC = 0.0;
        $riskNumI = 0.0; $riskDenI = 0.0;
        $riskNumD = 0.0; $riskDenD = 0.0;

        foreach ($controlMaturities as $cm) {
            $controlId = (int) $cm['control_id'];
            $maturity  = (int) $cm['maturity_level'];
            $weight    = (float) $cm['weight'];
            $c         = (int) $cm['confidentiality'];
            $i         = (int) $cm['integrity'];
            $d         = (int) $cm['availability'];
            $domainId  = (int) $cm['domain_id'];

            // Cumplimiento de preguntas
            $yes   = $complianceByControl[$controlId]['yes'] ?? 0;
            $no    = $complianceByControl[$controlId]['no']  ?? 0;
            $total = $yes + $no;
            $compliance = $total > 0 ? round($yes / $total * 100, 1) : 0.0;

            // Riesgo por dimensión
            $gap = 5.0 - $maturity;
            $riskNumC += $gap * $c * $weight;  $riskDenC += 5.0 * $c * $weight;
            $riskNumI += $gap * $i * $weight;  $riskDenI += 5.0 * $i * $weight;
            $riskNumD += $gap * $d * $weight;  $riskDenD += 5.0 * $d * $weight;

            $byControl[$controlId] = [
                'maturity'    => $maturity,
                'compliance'  => $compliance,
                'risk'        => round($gap * (($c + $i + $d) / 3) * $weight, 2),
                'weight'      => $weight,
                'domain_id'   => $domainId,
            ];

            // Acumular por dominio
            if (! isset($byDomain[$domainId])) {
                $byDomain[$domainId] = ['weighted_maturity' => 0.0, 'total_weight' => 0.0, 'risk' => 0.0, 'compliance_sum' => 0.0, 'control_count' => 0];
            }
            $byDomain[$domainId]['weighted_maturity'] += $maturity * $weight;
            $byDomain[$domainId]['total_weight']       += $weight;
            $byDomain[$domainId]['risk']               += $byControl[$controlId]['risk'];
            $byDomain[$domainId]['compliance_sum']     += $compliance;
            $byDomain[$domainId]['control_count']++;
        }

        // Madurez y cumplimiento por dominio
        $domainMaturities = [];
        foreach ($byDomain as $domainId => $d) {
            $mat = $d['total_weight'] > 0 ? $d['weighted_maturity'] / $d['total_weight'] : 0.0;
            $byDomain[$domainId]['maturity']   = round($mat, 2);
            $byDomain[$domainId]['compliance'] = $d['control_count'] > 0
                ? round($d['compliance_sum'] / $d['control_count'], 1) : 0.0;
            $domainMaturities[] = $mat;
        }

        // Madurez global
        $maturityGlobal = count($domainMaturities) > 0
            ? array_sum($domainMaturities) / count($domainMaturities) : 0.0;

        // Riesgo por dimensión (0-100%)
        $riskC = $riskDenC > 0 ? round($riskNumC / $riskDenC * 100, 2) : 0.0;
        $riskI = $riskDenI > 0 ? round($riskNumI / $riskDenI * 100, 2) : 0.0;
        $riskD = $riskDenD > 0 ? round($riskNumD / $riskDenD * 100, 2) : 0.0;
        $riskGlobal = round(($riskC + $riskI + $riskD) / 3, 2);

        return [
            'maturity_score' => round($maturityGlobal, 2),
            'risk_score'     => $riskGlobal,
            'risk_c'         => $riskC,
            'risk_i'         => $riskI,
            'risk_d'         => $riskD,
            'by_domain'      => $byDomain,
            'by_control'     => $byControl,
        ];
    }

    /** Etiqueta de nivel de madurez (escala 0-5 del profesor). */
    public static function maturityLabel(float $score): string
    {
        return match ((int) floor($score)) {
            0       => 'No existe',
            1       => 'Informal',
            2       => 'Parcial',
            3       => 'Definido',
            4       => 'Gestionado',
            default => 'Optimizado',
        };
    }

    public static function maturityColor(float $score): string
    {
        return match (true) {
            $score < 1 => 'danger',
            $score < 2 => 'danger',
            $score < 3 => 'warning',
            $score < 4 => 'info',
            $score < 5 => 'success',
            default    => 'success',
        };
    }

    public static function riskColor(float $risk): string
    {
        return match (true) {
            $risk >= 70 => 'danger',
            $risk >= 40 => 'warning',
            $risk >= 20 => 'info',
            default     => 'success',
        };
    }

    public static function riskLabel(float $risk): string
    {
        return match (true) {
            $risk >= 70 => 'Alto',
            $risk >= 40 => 'Medio',
            $risk >= 20 => 'Bajo',
            default     => 'Mínimo',
        };
    }
}
