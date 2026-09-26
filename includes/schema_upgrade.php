<?php
function upgradeSchema(PDO $db) {
    static $done = false;
    if ($done) return;
    $done = true;

    $cols = $db->query("SHOW COLUMNS FROM students")->fetchAll(PDO::FETCH_COLUMN);
    $add = [
        'gender'         => "VARCHAR(20) DEFAULT NULL",
        'date_of_birth'  => "DATE DEFAULT NULL",
        'section'        => "VARCHAR(10) DEFAULT NULL",
        'guardian_name'  => "VARCHAR(100) DEFAULT NULL",
        'guardian_phone' => "VARCHAR(20) DEFAULT NULL",
        'notes'          => "TEXT DEFAULT NULL",
    ];
    foreach ($add as $name => $def) {
        if (!in_array($name, $cols, true)) {
            $db->exec("ALTER TABLE students ADD COLUMN `$name` $def");
        }
    }
}

function nextStudentId(PDO $db) {
    $row = $db->query("SELECT student_id FROM students WHERE student_id LIKE 'ST%' ORDER BY id DESC LIMIT 1")->fetch();
    $n = 1;
    if ($row && preg_match('/(\d+)/', $row['student_id'], $m)) {
        $n = (int)$m[1] + 1;
    }
    return 'ST' . str_pad((string)$n, 3, '0', STR_PAD_LEFT);
}

function studentFieldOptions() {
    return [
        'programs' => ['BCA','BBA','BIT','CSIT','BIM'],
        'years'    => ['1st','2nd','3rd','4th'],
        'sections' => ['A','B','C','D'],
        'genders'  => ['Male','Female','Other'],
        'statuses' => ['Active','Inactive'],
    ];
}
