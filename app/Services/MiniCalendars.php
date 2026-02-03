<?php

namespace App\Services;

use Carbon\Carbon;

class MiniCalendars
{
    public static function show(
        int $year,
        int $month,
        int $day,
        string $view = 'month'
    ): string {
        $current = Carbon::create($year, $month, $day);

        return collect([-1, 0, 1])
            ->map(
                fn($offset) =>
                self::renderMonth(
                    $current->copy()->addMonths($offset),
                    highlight: $offset === 0,
                    view: $view,
                    selectedDate: $current
                )
            )
            ->implode('');
    }

    private static function renderMonth(
        Carbon $date,
        bool $highlight,
        string $view,
        Carbon $selectedDate
    ): string {
        $weekStarts = 0; // Sunday
        $daysInMonth = $date->daysInMonth;
        $firstDay = ($date->copy()->startOfMonth()->dayOfWeek + 7 - $weekStarts) % 7;

        $wrapOpen = '<div class="col-3 bg-white p-4 m-2 rounded text-center">';
        $wrapClose = '</div>';

        // 🔹 ADDED: selected month check
        $isSelectedMonth =
            request()->view === 'month'
            && (int) request()->year === $date->year
            && (int) request()->month === $date->month;

        $href = request()->fullUrlWithQuery([
            'view'  => 'month',
            'year'  => $date->year,
            'month' => $date->month,
            'day'   => 1,
        ]);

        // 🔹 ADDED: month title class
        $monthTitleClass = $isSelectedMonth ? 'selected-month' : '';

        $html = $wrapOpen . '<table class="calendar">';
        $html .= '<thead>';
        $html .= '<tr><th colspan="7" class="' . $monthTitleClass . '">'
            . '<a href="' . $href . '">' . $date->translatedFormat('F Y') . '</a>'
            . '</th></tr><tr>';
        
        for ($i = 0; $i < 7; $i++) {
            $classTh = '';
            if( $i == 5 || $i == 6 ) {
                $classTh = 'text-danger';
            }
            $html .= '<th class="'.$classTh.'">' . Carbon::now()->startOfWeek()->addDays($i)->format('D') . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        $d = 1 - $firstDay;

        while ($d <= $daysInMonth) {
            $html .= '<tr>';

            for ($i = 0; $i < 7; $i++, $d++) {
                if ($d < 1 || $d > $daysInMonth) {
                    $html .= '<td class="day_blank"></td>';
                    continue;
                }

                $cellDate = $date->copy()->day($d);
                $class = [];
                $classUrl = [];

                if ($cellDate->isWeekend()) {
                    $class[] = 'day_weekend';
                }

                // 🔹 TODAY
                if ($cellDate->isSameDay(now())) {
                    $classUrl[] = 'today';
                }

                // 🔹 SELECTED DATE
                if ($cellDate->isSameDay($selectedDate)) {
                    $classUrl[] = 'selected';
                }

                // 🔹 ADDED: SELECTED MONTH (Month view)
                if ($isSelectedMonth) {
                    $classUrl[] = 'selected';
                }

                // $url = request()->fullUrlWithQuery([
                //     'year'  => $cellDate->year,
                //     'month' => $cellDate->month,
                //     'day'   => $cellDate->day,
                // ]);

                $url = url()->current() . '?' . http_build_query(
                    array_merge(
                        request()->except('view'),
                        [
                            'year'  => $cellDate->year,
                            'month' => $cellDate->month,
                            'day'   => $cellDate->day,
                        ]
                    )
                );


                $class = implode(' ', $class);
                $classUrl = implode(' ', $classUrl);

                $html .= "
                    <td class='$class'>
                        <a class='$classUrl' href='$url'>
                            $d
                        </a>
                    </td>
                ";
            }

            $html .= '</tr>';
        }

        return $html . '</tbody></table>' . $wrapClose;
    }
}
