<footer class="py-3  mt-auto">
    <div class="container-fluid px-3">

    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<script>
    function formatDate(dateString) {
        var date = new Date(dateString);

        var weekday = date.toLocaleString('en-US', {
            weekday: 'short'
        }); // 'Wed'
        var day = date.getDate(); // '20'
        var month = date.toLocaleString('en-US', {
            month: 'short'
        }); // 'Nov'
        var hour = date.getHours() % 12 || 12; // Convert to 12-hour format (handle midnight correctly)
        var minute = date.getMinutes().toString().padStart(2, '0'); // Minutes with leading zero if needed
        var ampm = date.getHours() >= 12 ? 'pm' : 'am'; // AM/PM

        return `${weekday} ${day} ${month}, at ${hour}:${minute} ${ampm}`;
    }


    $(document).ready(function() {
        $('.view-reminder').click(function() {
            const reminderId = $(this).data('id');

            // AJAX Request
            $.get('getReminder.php', {
                reminderid: reminderId
            }, function(response) {
                const data = JSON.parse(response);

                // Populate Modal with Retrieved Data
                $('#courtname').text(data.courtname);
                $('#casename').text(data.casename);
                $('#casenumber').text(data.casenumber);
                $('#nextdate').text(formatDate(data.nextdate));
                $('#bringupdate').text(formatDate(data.bringupdate));
                $('#meetinglink').text(data.meetinglink);
                $('#notes').text(data.notes);

                // Show the Modal
                $('#reminderModal').modal('show');
            });
        });
    });

    $(document).ready(function() {
        // Show notification details in modal
        $('#notificationModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var notifId = button.data('id'); // Extract info from data-* attributes

            // AJAX request to fetch notification details
            $.ajax({
                url: 'php/notification-details', // PHP script to fetch details
                type: 'GET',
                dataType: 'json',
                data: {
                    id: notifId
                },
                success: function(response) {
                    $('#notifSubject').text(response.subject || 'No subject');
                    $('#notifText').text(response.text || 'No text');

                    // Update the notification as read
                    $.ajax({
                        url: 'php/notification-details', // PHP script to update the notification status
                        type: 'GET',
                        data: {
                            notifid: notifId
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching notification details:', status, error);
                }
            });
        });

        // Refresh the page when the modal is closed
        $('#notificationModal').on('hidden.bs.modal', function() {
            // Reload the page
            location.reload();
        });
    });
</script>


</body>

</html>