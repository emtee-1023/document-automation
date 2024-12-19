<footer class="">
    <div class="container-fluid px-3"></div>
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- Custom Scripts -->
<script src="js/scripts.js"></script>

<!-- Simple DataTables Script -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>

<!-- Toastr JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Summernote JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"></script>

<!-- Custom DataTables Demo Script -->
<script src="js/datatables-simple-demo.js"></script>

<script>
    // Global Toastr Configuration
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: "5000", // 5 seconds
    };

    // Format Date Helper Function
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = {
            weekday: 'short',
            day: 'numeric',
            month: 'short'
        };
        const timeOptions = {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        };
        return `${date.toLocaleDateString('en-US', options)}, at ${date.toLocaleTimeString('en-US', timeOptions)}`;
    }

    $(document).ready(function() {
        // Initialize Summernote
        $("#summernote").summernote({
            height: 200,
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["font", ["strikethrough", "superscript", "subscript"]],
                ["fontsize", ["fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["height", ["height"]],
                ["table", ["table"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });

        // View Reminder Click Event
        $(".view-reminder").click(function() {
            const reminderId = $(this).data("id");
            $.get("getReminder.php", {
                reminderid: reminderId
            }, function(response) {
                const data = JSON.parse(response);
                $("#courtname").text(data.courtname);
                $("#casename").text(data.casename);
                $("#casenumber").text(data.casenumber);
                $("#nextdate").text(formatDate(data.nextdate));
                $("#bringupdate").text(formatDate(data.bringupdate));
                $("#meetinglink").text(data.meetinglink);
                $("#notes").text(data.notes);
                $("#reminderModal").modal("show");
            }).fail(function() {
                toastr.error("Failed to load reminder details.", "Error");
            });
        });

        // Notification Modal Events
        $("#notificationModal").on("show.bs.modal", function(event) {
            const notifId = $(event.relatedTarget).data("id");
            $.get("php/notification-details", {
                id: notifId
            }, function(response) {
                $("#notifSubject").text(response.subject || "No subject");
                $("#notifText").text(response.text || "No text");
            }).fail(function() {
                toastr.error("Failed to load notification details.", "Error");
            });
        }).on("hidden.bs.modal", function() {
            location.reload();
        });
    });

    // Display Toastr Notifications from PHP Session
    <?php if (isset($_SESSION['success'])): ?>
        toastr.success("<?php echo $_SESSION['success']; ?>", "Success");
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        toastr.error("<?php echo $_SESSION['error']; ?>", "Error");
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</script>