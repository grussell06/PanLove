<?php
session_start();
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <link rel="stylesheet" href="../css/web.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Events - PanLove</title>
  </head>
  <body>
    <ul class="navBar">
        <li><a href="./homepage.php">Home</a></li>
        <li><a href="./matches.php">Matches</a></li>
        <li><a href="./messages.php">Messages</a></li>
        <li><a href="./settings.php">Settings</a></li>
        <?php
        if (isset($_SESSION['user_id'])) {
            echo '<li><a href="./profile.php">Profile</a></li>';
        } else {
            echo '<li><a href="./login.php">Login</a></li>';
        }
        ?>
    </ul>

    <div class="content">
        <h1>Events Calendar</h1>

        <?php
        // Get current month and year, or from GET parameters
        $month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
        $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

        // Validate month and year
        if ($month < 1 || $month > 12) $month = date('n');
        if ($year < 1900 || $year > 2100) $year = date('Y');

        // Calculate previous and next month
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        // Get number of days in month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Get first day of month (0 = Sunday, 6 = Saturday)
        $firstDayOfMonth = date('w', strtotime("$year-$month-01"));

        // Month names
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        ?>

        <div class="calendar-container">
            <div class="calendar-header">
                <a href="?month=<?php echo $prevMonth; ?>&year=<?php echo $prevYear; ?>" class="nav-btn">&larr; Previous</a>
                <h2><?php echo $monthNames[$month] . ' ' . $year; ?></h2>
                <a href="?month=<?php echo $nextMonth; ?>&year=<?php echo $nextYear; ?>" class="nav-btn">Next &rarr;</a>
            </div>

            <div class="calendar">
                <!-- Day headers -->
                <div class="day-header">Sun</div>
                <div class="day-header">Mon</div>
                <div class="day-header">Tue</div>
                <div class="day-header">Wed</div>
                <div class="day-header">Thu</div>
                <div class="day-header">Fri</div>
                <div class="day-header">Sat</div>

                <?php
                // Add empty cells for days before the first day of the month
                for ($i = 0; $i < $firstDayOfMonth; $i++) {
                    echo '<div class="day empty"></div>';
                }

                // Add cells for each day of the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                    $dayClass = $isToday ? 'day today' : 'day';

                    echo "<div class='$dayClass'>$day</div>";
                }

                // Fill remaining cells to complete the grid
                $totalCells = $firstDayOfMonth + $daysInMonth;
                $remainingCells = 42 - $totalCells; // 6 rows * 7 days = 42 cells

                for ($i = 0; $i < $remainingCells; $i++) {
                    echo '<div class="day empty"></div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>