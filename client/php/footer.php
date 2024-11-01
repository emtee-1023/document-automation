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
            $(document).ready(function() {
            // Show notification details in modal
            $('#notificationModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Button that triggered the modal
                var notifId = button.data('id'); // Extract info from data-* attributes

                // AJAX request to fetch notification details
                $.ajax({
                    url: 'php/notification-details', // PHP script to fetch details
                    type: 'GET',
                    dataType: 'json',
                    data: { id: notifId },
                    success: function(response) {
                        $('#notifSubject').text(response.subject || 'No subject');
                        $('#notifText').text(response.text || 'No text');
                        
                        // Update the notification as read
                        $.ajax({
                            url: 'php/notification-details', // PHP script to update the notification status
                            type: 'GET',
                            data: { notifid: notifId }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching notification details:', status, error);
                    }
                });
            });

                // Refresh the page when the modal is closed
                $('#notificationModal').on('hidden.bs.modal', function () {
                    // Reload the page
                    location.reload();
                });
            });

        </script>

    </body>
</html>