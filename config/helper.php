<?php
/**
 * Menentukan URL tujuan card homepage berdasarkan kata kunci di judul.
 *
 * @param string $title Judul card
 * @return string URL tujuan
 */
function getCardLink(string $title): string
{
    $lower = strtolower($title);

    if (strpos($lower, 'survey') !== false) {
        return 'survey.php';
    }

    if (strpos($lower, 'aduan') !== false) {
        return 'index.php#form-permohonan';
    }

    if (strpos($lower, 'informasi') !== false || strpos($lower, 'dokumen') !== false) {
        return 'informasi.php';
    }

    return '#';
}
?>