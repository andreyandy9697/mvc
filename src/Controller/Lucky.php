<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Lucky extends AbstractController
{
    #[Route('/lucky', name: 'lucky')]
    public function number(): Response
    {
        $month_dyk = str_pad(random_int(1, 12), 2, '0', STR_PAD_LEFT);

        $monthImages = [
            '01' => 'img/01_Anemon.jpg',
            '02' => 'img/02_Dahlia_Anemon.jpg',
            '03' => 'img/03_Krabbtaska.jpg',
            '04' => 'img/04_4427Dark.jpg',
            '05' => 'img/05_2355Col.jpg',
            '06' => 'img/06_2950ACD.jpg',
            '07' => 'img/07_3733Col.jpg',
            '08' => 'img/08_Krabbtaska2.jpg',
            '09' => 'img/09_4847Col.jpg',
            '10' => 'img/10_BiggerGBR.jpg',
            '11' => 'img/11_sjostjarna.jpg',
            '12' => 'img/12_jag.jpg',
        ];

        $currentImage = $monthImages[$month_dyk] ?? 'img/default.jpg';

        $date = date('Y') . '-' . $month_dyk . '-01';
        $timestamp = strtotime($date);
        $firstDayOfMonth = strtotime(date('Y-m-01', $timestamp));
        $monthStr = date('F', $firstDayOfMonth);
        $yearStr = date('Y', $firstDayOfMonth);

        $firstDayOfWeek = date('N', $firstDayOfMonth);
        $startDate = ($firstDayOfWeek == 1) ? $firstDayOfMonth : strtotime('last Monday', $firstDayOfMonth);
        $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));
        $endDate = (date('N', $lastDayOfMonth) == 7) ? $lastDayOfMonth : strtotime('next Sunday', $lastDayOfMonth);

        $calendarRows = '';
        $currentDay = $startDate;

        while ($currentDay <= $endDate) {
            $weekNum = date('W', $currentDay);
            $calendarRows .= "<tr><td style='text-align:center; color:rgb(0, 0, 0);'>{$weekNum}</td>";

            for ($i = 0; $i < 7; $i++) {
                $dayNum = date('j', $currentDay);
                $dayOfYear = date('z', $currentDay) + 1;
                $dayName = date('l', $currentDay);
                $monthCheck = date('m', $currentDay) === $month_dyk;

                $style = 'text-align:center; color: yellow; vertical-align:middle;';
                if (!$monthCheck) {
                    $style .= ' color:gray;';
                }
                if ($dayName === 'Sunday') {
                    $style .= ' color:red;';
                }

                $calendarRows .= "
                    <td style=\"$style\">
                        <div style='font-size: 18px; font-weight: bold;'>$dayNum</div>
                        <div style='font-size: 12px; color: grey;'>$dayOfYear</div>
                    </td>";

                $currentDay = strtotime('+1 day', $currentDay);
            }

            $calendarRows .= '</tr>';
        }

        return $this->render('lucky_calendar.html.twig', [
            'monthStr' => $monthStr,
            'yearStr' => $yearStr,
            'monthNum' => $month_dyk,
            'currentImage' => $currentImage,
            'calendarRows' => $calendarRows,
        ]);

    }
}
