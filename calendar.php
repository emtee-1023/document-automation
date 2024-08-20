<?php include 'php/header.php';?>

<div id="layoutSidenav">
            <?php include 'php/sidebar.php';?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Calendar</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index">Home</a></li>
                            <li class="breadcrumb-item active">Calendar</li>
                        </ol>
                        <ol class="breadcrumb mb-2">
                            <li class="breadcrumb-item active"></li>
                        </ol>
                        <p class="text-secondary fs-6"></p>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                            <iframe src="https://calendar.google.com/calendar/embed?src=your_calendar_id&ctz=your_timezone"
style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>

                            </div>
                        </div>
                    </div>
                </main>
