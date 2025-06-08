// Enhanced stock management script with delete functionality only
$(document).ready(function() {
    // Select all checkboxes functionality
    $("#selectAll").change(function() {
        $(".stock-checkbox").prop('checked', $(this).prop("checked"));
        updateDeleteButtonState();
    });

    // Update delete button state when individual checkboxes change
    $(document).on('change', '.stock-checkbox', function() {
        updateDeleteButtonState();
    });

    // Function to update delete button state
    function updateDeleteButtonState() {
        const checkedBoxes = $(".stock-checkbox:checked").length;
        if (checkedBoxes > 0) {
            $("#deleteSelected").prop('disabled', false).removeClass('disabled');
        } else {
            $("#deleteSelected").prop('disabled', true).addClass('disabled');
        }
    }

    // Initialize delete button state
    updateDeleteButtonState();

    // Delete single row
    window.deleteRow = function(element) {
        const row = $(element).closest('tr');
        const recordId = row.find('td:nth-child(2)').text().trim(); // Get record ID
        
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: 'delete_pole_record.php',
                type: 'POST',
                data: { id: recordId },
                success: function(response) {
                    row.fadeOut(400, function() {
                        $(this).remove();
                        // Update IDs of remaining rows
                        updateRowIds();
                    });
                },
                error: function(xhr, status, error) {
                    alert('Error deleting record: ' + error);
                }
            });
        }
    };

    // Delete selected records
    $("#deleteSelected").click(function() {
        const selectedIds = [];
        
        $(".stock-checkbox:checked").each(function() {
            const row = $(this).closest('tr');
            const id = row.find('td:nth-child(2)').text().trim();
            selectedIds.push(id);
        });
        
        if (selectedIds.length === 0) {
            alert('Please select records to delete');
            return;
        }
        
        if (confirm(`Are you sure you want to delete ${selectedIds.length} selected records?`)) {
            $.ajax({
                url: 'delete_multiple_pole_records.php',
                type: 'POST',
                data: { ids: selectedIds },
                success: function(response) {
                    $(".stock-checkbox:checked").closest('tr').fadeOut(400, function() {
                        $(this).remove();
                        // Update IDs of remaining rows
                        updateRowIds();
                        // Update "Select All" checkbox and delete button state
                        $("#selectAll").prop('checked', false);
                        updateDeleteButtonState();
                    });
                },
                error: function(xhr, status, error) {
                    alert('Error deleting records: ' + error);
                }
            });
        }
    });

    // Function to update row IDs after deletion
    function updateRowIds() {
        let i = 1;
        $('#stockTable tbody tr').each(function() {
            $(this).find('td:nth-child(2)').text(i);
            i++;
        });
    }
});