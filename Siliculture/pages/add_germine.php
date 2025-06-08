<?php   
require_once '../../logs/backend/auth_check.php'; // Include the authentication check 
checkUserAuth('Siliculture'); // Check if the user is logged in and has the required role  
?>   

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Germination Record</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/add-germine.css">
    <style></style>
</head>
<body>
    <?php include'../menu/menu.php'; ?>
    
    <div class="main-content">
        
        <div class="form-container">
            
            
            <!-- Progress Indicator
            
            
            <!-- Form Wrapper -->
            <div class="form-wrapper-pp">
                <div class="form-banner">
                    <h2><i class="fa fa-seedling"></i> Germination Tracking Form</h2>
                    <p>Fill in the details below to create a new germination record</p>
                </div>
                
                <div class="form-content">
                   
                    
                    <form action="../backend/process_germination.php" method="POST" id="germinationForm">
                        <div class="form-grid">
                            <!-- Tree Name -->
                            <div class="form-group">
                                <label for="tree_name" class="form-label">
                                    <i class="fa fa-tree"></i> Tree Name <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <i class="fa fa-leaf input-icon"></i>
                                    <input type="text" id="tree_name" name="tree_name" class="form-input" 
                                           placeholder="Enter tree species name" required>
                                </div>
                            </div>
                            
                            <!-- Plant Date -->
                            <div class="form-group">
                                <label for="plant_date" class="form-label">
                                    <i class="fa fa-calendar"></i> Plant Date <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <i class="fa fa-calendar-o input-icon"></i>
                                    <input type="date" id="plant_date" name="plant_date" class="form-input" required>
                                </div>
                            </div>
                            
                            <!-- Germination Start Date -->
                            <div class="form-group">
                                <label for="germination_start" class="form-label">
                                    <i class="fa fa-play-circle"></i> Germination Start Date <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <i class="fa fa-calendar-plus-o input-icon"></i>
                                    <input type="date" id="germination_start" name="germination_start" class="form-input" required>
                                </div>
                            </div>
                            
                            <!-- Germination End Date -->
                            <div class="form-group">
                                <label for="germination_end" class="form-label">
                                    <i class="fa fa-stop-circle"></i> Germination End Date
                                </label>
                                <div class="input-group">
                                    <i class="fa fa-calendar-check-o input-icon"></i>
                                    <input type="date" id="germination_end" name="germination_end" class="form-input">
                                </div>
                            </div>
                            
                            <!-- Number of Seeds -->
                            <div class="form-group">
                                <label for="num_seeds" class="form-label">
                                    <i class="fa fa-hashtag"></i> Number of Seeds <span class="required">*</span>
                                </label>
                                <div class="input-group">
                                    <i class="fa fa-sort-numeric-asc input-icon"></i>
                                    <input type="number" id="num_seeds" name="num_seeds" class="form-input" 
                                           placeholder="Enter number of seeds" min="1" required>
                                </div>
                            </div>
                            
                            <!-- Soil Type -->
                            <div class="form-group">
                                <label for="soil_type" class="form-label">
                                    <i class="fa fa-globe"></i> Soil Type <span class="required">*</span>
                                </label>
                                <select id="soil_type" name="soil_type" class="form-select" required>
                                    <option value="">Select soil type</option>
                                    <option value="Sandy">Sandy Soil</option>
                                    <option value="Clay">Clay Soil</option>
                                    <option value="Loam">Loam Soil</option>
                                    <option value="Silt">Silt Soil</option>
                                    <option value="Peaty">Peaty Soil</option>
                                    <option value="Chalky">Chalky Soil</option>
                                    <option value="Mixed">Mixed Soil</option>
                                    <option value="Potting Mix">Potting Mix</option>
                                    <option value="Compost">Compost Rich</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            
                            <!-- Comments -->
                            <div class="form-group full-width">
                                <label for="comments" class="form-label">
                                    <i class="fa fa-comment"></i> Comments / Notes
                                </label>
                                <textarea id="comments" name="comments" class="form-textarea" 
                                          placeholder="Add any additional notes about the germination process, environmental conditions, or observations..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Form Buttons -->
                        <div class="btn-container">
                            <a href="germ.php" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" name="germination" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Form validation and enhancement
        document.getElementById('germinationForm').addEventListener('submit', function(e) {
            const plantDate = new Date(document.getElementById('plant_date').value);
            const germStartDate = new Date(document.getElementById('germination_start').value);
            const germEndDate = document.getElementById('germination_end').value;
            
            // Validate dates
            if (germStartDate < plantDate) {
                e.preventDefault();
                alert('Germination start date cannot be earlier than plant date');
                return;
            }
            
            if (germEndDate) {
                const germEndDateObj = new Date(germEndDate);
                if (germEndDateObj < germStartDate) {
                    e.preventDefault();
                    alert('Germination end date cannot be earlier than start date');
                    return;
                }
            }
            
            // Validate number of seeds
            const numSeeds = parseInt(document.getElementById('num_seeds').value);
            if (numSeeds <= 0) {
                e.preventDefault();
                alert('Number of seeds must be greater than 0');
                return;
            }
        });
        
        // Auto-update progress indicator based on form completion
        function updateProgress() {
            const requiredFields = document.querySelectorAll('[required]');
            let filledFields = 0;
            
            requiredFields.forEach(field => {
                if (field.value.trim() !== '') {
                    filledFields++;
                }
            });
            
            const progressSteps = document.querySelectorAll('.progress-step');
            const progressPercentage = (filledFields / requiredFields.length) * 100;
            
            progressSteps.forEach((step, index) => {
                if (index < Math.ceil(progressPercentage / 33.33)) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });
        }
        
        // Add event listeners to required fields
        document.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('input', updateProgress);
            field.addEventListener('change', updateProgress);
        });
        
        // Set current date as default for plant date
        document.getElementById('plant_date').value = new Date().toISOString().split('T')[0];
        
        // Update progress on page load
        updateProgress();
    </script>
</body>
</html>