<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
<link href="styles/main.css" rel="stylesheet" type="text/css">

<body class="quest">
  <div class="header">
    <div class="ellipses">
      <div class="ellipse9"></div>
      <div class="ellipse10"></div>
      <div class="ellipse11"></div>
    </div>
    <div class="ellipse12"></div>
  </div>
<?php 
# set the date by using the provided date from the URL
$thisday = $_GET['date'];
?>

<!-- Form with all questions by using JavaScript to enable a stepper view-->
<form id="regForm" action=<?php echo "daily_questionnaire.php?date=$thisday"?> method="post">
  <!-- One "tab" for each step in the form: -->
  <div class="tab">
    <h1>How do you feel today?</h1>
    <div class="form-radio">
        <div class="radio-item-list">
            <span class="radio-item">
                <input class="hidden" type="radio" name="feeling" value="4" id="feeling_good" required/>
                <label for="feeling_good">&#128522;</label>
            </span>
            <span class="radio-item active">
                <input class="hidden" type="radio" name="feeling" value="3" id="feeling_okay"/>
                <label for="feeling_okay">&#128528;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="feeling" value="2" id="feeling_nah" />
                <label for="feeling_nah">&#128533;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="feeling" value="1" id="feeling_bad" />
                <label for="feeling_bad">&#128543;</label>
            </span>
        </div>
    </div>
  </div>
  <div class="tab">
    <h1>How did you sleep last night?</h1>
    <div class="form-radio">
        <div class="radio-item-list">
            <span class="radio-item">
                <input class="hidden" type="radio" name="sleep" value="4" id="sleep_good" required/>
                <label for="sleep_good">&#128522;</label>
            </span>
            <span class="radio-item active">
                <input class="hidden" type="radio" name="sleep" value="3" id="sleep_okay"/>
                <label for="sleep_okay">&#128528;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" class="hidden" type="radio" name="sleep" value="2" id="sleep_nah" />
                <label for="sleep_nah">&#128533;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="sleep" value="1" id="sleep_bad" />
                <label for="sleep_bad">&#128543;</label>
            </span>
        </div>
    </div>
    <h1>How long did you sleep last night?</h1>
    <div class="slidecontainer">
        <span class="slidecontainer">
            <input name= "sleep_time" type="range" min="0" max="20" value="8" class="slider" id="myRange">
            <p>Duration: <span id="demo"></span> hours</p>
        </span>
   </div>
  </div>
  <div class="tab">
    <h1>Did you do sports today?</h1>
    <div class="form-radio">
        <div class="radio-item-list">
            <span class="radio-item">
                <input class="hidden" type="radio" name="sports" value=1 id="yes" required/>
                <label for="yes">&#128170;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="sports" value=0 id="no"/>
                <label for="no">&#129364;</label>
            </span>
        </div>
    </div>
    <h1>If yes, what did you do?</h1>
    <div class="form-radio">
        <div class="radio-item-list">
            <span class="radio-item">
                <input class="hidden" type="radio" name="sports_kind" value="4" id="running" />
                <label for="running">&#127939;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="sports_kind" value="3" id="lift_weights"/>
                <label for="lift_weights">&#127947;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="sports_kind" value="2" id="swimming" />
                <label for="swimming">&#127946;</label>
            </span>
            <span class="radio-item">
                <input class="hidden" type="radio" name="sports_kind" value="1" id="biking" />
                <label for="biking">&#128692;</label>
            </span> <br> <br>
            <input placeholder="Other..." type="text" name="other" id="kind_of_sports">
        </div>
    </div>
  </div>
  <div class="tab">
    <h1>What is your weight today? (kg)</h1>
    <div class="form-radio">
        <div class="radio-item-list">
            <span class="radio-item">
                <input name="weight" placeholder="Add weight..." type="number" id="weight" required>
            </span>
        </div>
    </div>
  </div>
  <div class="tab">
    <h1>Would you like to note down anything else?</h1>
    <div class="form-radio">
        <div class="radio-item-list">
            <span class="radio-item">
                <input name="individual_entry" placeholder="Add entry..." type="text" name="diary_entry" id="diary_entry">
            </span>
        </div>
    </div>
  </div>
  <div style="overflow:auto;">
    <div style="float:right;">
      <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
    </div>
  </div>
  <!-- Circles which indicates the steps of the form: -->
  <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
  </div>
</form>

<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Show the current tab
var slider = document.getElementById("myRange");
var output = document.getElementById("demo");
output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}

// showTab function enables to show each time the actual step
function showTab(n) {
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //Previous button is ony available from the second step onwards and Next button gets submit on the last step
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // Fuction is used to declare which step should be showen
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // submitt form after reaching the last step:
  if (currentTab >= x.length) {
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // validation of the form fields:
  var x, y, i;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // check every input field in the current tab -> if required an answer needs to be selected prior moring on
  let currentName;
  let valid = true;
  for (i = 0; i < y.length; i++) {
      if (y[i].name == "weight" && y[i].value < 0){
        valid = false;
        break;
      }
      if (y[i].type == 'radio') {
        if (currentName != y[i].name && y[i].required) {
            if (!valid) {
                break;
            }

            valid = false;
            currentName = y[i].name;
        }
        
        if (y[i].checked) {
            valid = true;
        }
      }
      else if(y[i].required && !y[i].value){
          valid = false;
      }
  }

  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // remove the "active" class of all steps
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //add the "active" class on the current step:
  x[n].className += " active";
}

</script>

<!--Footer-->
  <div class="footer">
    <p> &copy; Copyright 2021 | Linea Schmidt, Simon Shabo
      <a href="About_this_website.html"> About this website</a>
    </p>
  </div>
</body>
</html>
