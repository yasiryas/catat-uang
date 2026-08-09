<?php

use Carbon\Carbon;

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi mata uang Rupiah Indonesia.
     */
    function rupiah(float|int|string|null $value, bool $withRp = true): string
    {
        $value = max(0, (float) $value);
        $formatted = number_format($value, 0, ',', '.');

        return $withRp ? 'Rp ' . $formatted : $formatted;
    }
}

if (! function_exists('tanggal')) {
    /**
     * Format tanggal ke bentuk Indonesia, opsional dengan jam.
     */
    function tanggal(string|Carbon|null $date, bool $withTime = false): string
    {
        if (! $date) {
            return '-';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $carbon->locale('id')->isoFormat($withTime ? 'DD MMMM YYYY, HH:mm' : 'DD MMMM YYYY');
    }
}

if (! function_exists('jam')) {
    /**
     * Format jam ke bentuk Indonesia (HH:mm).
     */
    function jam(string|Carbon|null $date): string
    {
        if (! $date) {
            return '-';
        }

        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);

        return $carbon->locale('id')->isoFormat('HH:mm');
    }
}