<?php
$date = new DateTime();

function renderCalender() {
    $date->setDate(1);

    $monthDays = $document->querySelector('.days');

    $lastDay = new DateTime()
    $lastDay->setDate($date('Y'), $date('m') +1, 0);

    $prevLastDay = new DateTime(
        $date('Y'),
        $date('m'),
        0
    )->getdate();

    $firstDayIndex = $date->getdate();

    $lastDayIndex = new DateTime(
        $date('Y'),
        $date('m'),
        0
    )->getdate();

    $nextDays = 7 - lastDayIndex -1;

    $months = 
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
    ];
    $document->queryselector('.date h1')->innerHTML = $months[$date('m')];

    $document->querySelector('.date p')->innerHTML = new DateTime()->format('Y-m-d');

    $days = "";

    for($i =1; $i <= $lastDay; $i++){
        if($i == new DateTime()->getdate() && $date('m' == new DateTime('m')){
            $days += '<div class="today"> <a href="daily_questionnaire.html" class="fill-calendarday">.$i.</a></div>';)
        } else{
            $days +='<div>.$i.</div>';
        }
    }

    for($j = 1; $j <= $nextDays; $j++){
        $days += '<div class="next-date">.$j.</div>';
        $monthDays->innerHTML = $days;
    }
};

$document->queryselector('.prev');

$document->queryselector('next');

renderCalender();

?>