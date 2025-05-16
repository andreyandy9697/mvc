<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Lucky extends AbstractController
{
    #[Route('/lucky', name: 'lucky')]
    public function number(): Response
    {
        $semester = str_pad(random_int(1, 12), 2, '0', STR_PAD_LEFT);

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

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Mars',
            4 => 'April',
            5 => 'Maj',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Augusti',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'December',
        ];

        // Svenska månadsnamn
        $monthName = $months[(int) $semester];

        $currentImage = $monthImages[$semester] ?? 'img/default.jpg';

        $date = date('Y').'-'.$semester.'-01';
        $timestamp = strtotime($date);
        $firstDayOfMonth = strtotime(date('Y-m-01', $timestamp));
        $monthStr = $monthName;
        $yearStr = date('Y', $firstDayOfMonth);

        $firstDayOfWeek = date('N', $firstDayOfMonth);
        $startDate = (1 === $firstDayOfWeek) ? $firstDayOfMonth : strtotime('last Monday', $firstDayOfMonth);
        $lastDayOfMonth = strtotime(date('Y-m-t', $firstDayOfMonth));
        $endDate = (7 === date('N', $lastDayOfMonth)) ? $lastDayOfMonth : strtotime('next Sunday', $lastDayOfMonth);

        $calendarRows = '';
        $currentDay = $startDate;

        while ($currentDay <= $endDate) {
            $weekNum = date('W', $currentDay);
            $calendarRows .= "<tr><td style='text-align:center; color:rgb(117, 234, 255);'>{$weekNum}</td>";

            for ($i = 0; $i < 7; ++$i) {
                $dayNum = date('j', $currentDay);
                $dayOfYear = date('z', $currentDay) + 1;
                $dayName = date('l', $currentDay);
                $monthCheck = date('m', $currentDay) === $semester;

                $style = 'text-align:center; color: yellow; vertical-align:middle;';
                if (!$monthCheck) {
                    $style .= ' color:rgb(110, 187, 129)';
                }
                if ('Sunday' === $dayName) {
                    $style .= ' color:rgb(255, 52, 52);';
                }

                $calendarRows .= "
                    <td style=\"{$style}\">
                        <div style='font-size: 18px; font-weight: bold;'>{$dayNum}</div>
                        <div style='font-size: 12px; color:rgb(110, 187, 129);'>{$dayOfYear}</div>
                    </td>";

                $currentDay = strtotime('+1 day', $currentDay);
            }

            $calendarRows .= '</tr>';
        }

        return $this->render('lucky_calendar.html.twig', [
            'monthStr' => $monthStr,
            'yearStr' => $yearStr,
            'monthNum' => $semester,
            'currentImage' => $currentImage,
            'calendarRows' => $calendarRows,
        ]);
    }

    #[Route('/api/quote', name: 'api_quote')]
    public function jsonNumber(): JsonResponse
    {
        $monthNum = random_int(1, 12);
        $meddela = random_int(1, 3);

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Mars',
            4 => 'April',
            5 => 'Maj',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Augusti',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'December',
        ];

        // Månadsnamn på svenska
        $monthName = $months[$monthNum];

        // Dagens tid och datum
        $day = date('Y-m-d');
        $time = date('H:i:s');

        // Meddelande
        $messages = [
            1 => "Viktigt meddelande!!! Idag är det {$day}, klockan {$time}! Planera din semester för {$monthName} månaden nu!!!",
            2 => "Viktigt meddelande!!! Idag är det {$day}, klockan {$time}! Din semestermånad är {$monthName}",
            3 => "Viktigt meddelande!!! Idag är det {$day}, klockan {$time}! Sist du hade semester var i {$monthName} förra året! Planera din nästa semester nu!!!",
        ];

        $message = $messages[$meddela];

        return new JsonResponse([
            'Semester planering för månad' => $monthNum,
            'Planerings meddelande' => $message,
        ]);
    }
}
