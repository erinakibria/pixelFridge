<?php
    session_start();
    include 'db.php';

    if(isset($_POST['id'])){
        echo $_POST['id'];
    }

    if(isset($_SESSION['username'])){
        $username = $_SESSION['username'];

    }
    else{
        header("Location: index.php");
    }
?>

<DOCTYPE! html>
    <html>
        <head>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
            <link rel='stylesheet' href='fridge.css'>
	    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	    <script src='fridge.js'></script>
  	    <title>Fridge dashboard</title>
        </head>
        <body>
        <?php
            $sql = $conn->prepare("select fname from users where username=?");
            $sql->bind_param("s", $uname);
            $uname = $username;
       	    $sql->execute();
            $result = $sql->get_result();
            $record = $result->fetch_row();
            echo "<h2>Welcome to your fridge, $record[0]!</h2>";
        ?>
        <p class='instruct'>Search for items and log them into your fridge using the menu on the right</p>
        <?php
            echo "<form action='login.php' method='post'>
                    <input id='logout' class='logout' type='submit' name='logout' value='Logout'>
                </form>"
        ?>
        <div class='fridge-home'>
            <div class='col1'>
            <div class='button-box'><button id='open-button'>Open</button></div>
            <div class='button-box'><button id='edit-button'>Edit item</button></div>
            <div class='button-box'><button id='remove-button'>Remove item</button></div>
            <div class='button-box'><button id='clear-button'>Clear items</button></div>
            </div>
            <div class='col2-closed' id='fridge-display'>
            <div class='delete-confirm' id='delete-modal'>
                <p>Delete item?</p>
                <img src='pics/items/apple.png' id='delete-pic'>
                <p id='delete-name'>Apple</p>
                <button onclick='removeItem(this.id)' id='remove-yes'>Yes</button>
                <button onclick='removeItem(this.id)' id='remove-no'>No</button>
            </div>
            <div class='clear-confirm' id='clear-modal'>
                <p>Which items would you like to clear?</p>
                <form id="clr-form">
                    <div>
                        <input type="checkbox" id="exp-clr" name="exp-clr" value="Expired">
                        <label for="exp-clr"> Expired</label>
                    </div><br><br>
                    <div>
                    <input type="checkbox" id="out-clr" name="out-clr" value="Out of stock">
                    <label for="out-clr"> Out of stock</label>
                    </div><br><br>
                    <div>
                    <input type="checkbox" id="all-clr" name="all-clr" value="All">
                    <label for="all-clr"> All</label>
                    </div><br><br>
                    <input type="submit" value="Submit">
                </form>
            </div>
            <div class='fridge-grid' id='fridge-grid'>
                <div class='fridge-r1' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon1'>X</a><p class='oos'></p></div> 
                <div class='fridge-r1' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon2'>X</a><p class='oos'></p></div>
                <div class='fridge-r1' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon3'>X</a><p class='oos'></p></div>  
                <div class='fridge-r2' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon4'>X</a><p class='oos'></p></div>
                <div class='fridge-r2' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon5'>X</a><p class='oos'></p></div>
                <div class='fridge-r2' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon6'>X</a><p class='oos'></p></div>  
                <div class='fridge-r3' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon7'>X</a><p class='oos'></p></div>
                <div class='fridge-r3' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon8'>X</a><p class='oos'></p></div>
                <div class='fridge-r3' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon9'>X</a><p class='oos'></p></div>  
                <div class='fridge-r4' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon10'>X</a><p class='oos'></p></div>
                <div class='fridge-r4' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon11'>X</a><p class='oos'></p></div>
                <div class='fridge-r4' id='empty' onmouseover='displayItemInfo(this.id)'><img src='pics/biink.png'><a onclick='confirmRemoval(this.id)' class='remove' id='remove-icon12'>X</a><p class='oos'></p></div>
            </div>
            <div id='fridge-pag-arrows'>
                <a class='pag1' id='fridge-pag1' onclick='prevPg(this.id)'><</a>
                <a class='pag2' id='fridge-pag2' onclick='nextPg(this.id)'>></a>
            </div>
            <div class='item-info' id='item-info'>
                <div id='info1'>
                    <p id='info-name'>Name:</p>
                    <p>Quantity:</p>
                    <p>Log date:</p>
                    <p>Made on:</p>
                    <p>Expires on:</p>
                    <p>Life remaining:</p>
                </div>
                <div>
                    <p id='iname'></p>
                    <p id='iquantity'></p>
                    <p id='ilog'></p>
                    <p id='imade'></p>
                    <p id='iexp'></p>
                    <p id='ilife'></p>
                </div>
            </div>
	    </div>
            <div id='col3-home' class='col3-home'>
                <button id='view-button' >View report</button>
		<button id='add-button'>Add item</button>
                <button id='history-button'>History</button>
                <label id='sort-label' for="sort-options">Sort items by:</label>
                <form id='item-sort-form' method='post' action=''>
                <select name="sort-options" id="sort-options">
                  <option value="1">Log date (Recent first)</option>
                  <option value="2">Log date (Oldest first)</option>
                  <option value="3">Remaining life (High to Low)</option>
                  <option value="4">Remaining life (Low to High)</option>
                  <option value="5">Quantity (High to Low)</option>
                  <option value="6">Quantity (Low to High)</option>
                </select>
                <button id='add-button' type='submit'>Apply</button>
		</form>
            </div>
            <div id='col3-report' class='col3-report'>
                <div class='report-title'>
                <a id='report-back'><</a>
                <p><strong>Fridge Report</strong></p>
                </div>
                <button id='collapse1' class='report-collapsible' onclick='showReport(this.id)'><img src='pics/down.png'>Items expiring</button>
                <div class='report-info' id='collapse1-report'>
                <ul>
          
                </ul>
                </div>
                <button id='collapse2' class='report-collapsible' onclick='showReport(this.id)'><img src='pics/down.png'>Expired items</button>
                <div class='report-info' id='collapse2-report'>
                <ul>

                </ul>
                </div>
                <button id='collapse3' class='report-collapsible' onclick='showReport(this.id)'><img src='pics/down.png'>Recipe inspiration</button>
                <div class='report-info' id='collapse3-report'>
                <ul>

                </ul>
                </ul>
                </div>
                <button id='collapse4' class='report-collapsible' onclick='showReport(this.id)'><img src='pics/down.png'>Logged today</button>
                <div class='report-info' id='collapse4-report'>
                <ul>
                </ul>
                </div>
                <button id='collapse5' class='report-collapsible' onclick='showReport(this.id)'><img src='pics/down.png'>Restock list</button>
                <div class='report-info' id='collapse5-report'>
                <ul>
                    <!-- <li>Coffee</li>
                    <li>Tea</li>
                    <li>Milk</li> -->
                </ul>
                </div>
                <!-- <button id='collapse6' class='report-collapsible' onclick='showReport(this.id)'><img src='pics/down.png'>Item check-up</button>
                <div class='report-info' id='collapse6-report'>
                <ul>
                    <li>Coffee</li>
                    <li>Tea</li>
                    <li>Milk</li>
                </ul>
                </div> -->
            </div>
            <div id='col3-add' class='col3-add'>
                    <a id='add-back'><</a>
                    <p>Add item by search:</p>
                    <input type='text' name='item' onkeyup='populateSearch(this.value)'><br><br>
                    <p id='item-name'>Placeholder</p>
                    <div class='items-box' id='items-box'>
                        <div class="grid-item" id='gi1' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp1' src='pics/biink.png'></div>
                        <div class="grid-item" id='gi2' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp2' src='pics/biink.png'></div>
                        <div class="grid-item" id='gi3' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp3' src='pics/biink.png'></div>  
                        <div class="grid-item" id='gi4' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp4' src='pics/biink.png'></div>
                        <div class="grid-item" id='gi5' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp5' src='pics/biink.png'></div>
                        <div class="grid-item" id='gi6' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp6' src='pics/biink.png'></div>  
                        <div class="grid-item" id='gi7' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp7' src='pics/biink.png'></div>
                        <div class="grid-item" id='gi8' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp8' src='pics/biink.png'></div>
                        <div class="grid-item" id='gi9' onmouseover='dispName(this.id)' onclick='itemAdd(this.id)'><img id='disp9' src='pics/biink.png'></div>  
                    </div><br>
                    <div id='pag-arrows'>
                        <a id='search-pag' onclick='prevPg(this.id)'><</a>
                        <a id='search-pag' onclick='nextPg(this.id)'>></a>
                    </div>
                    <p id='num-res'></p>
	    </div>
            <div id='col3-history' class='col3-history'>
		<a id='history-back'><</a>
	                <p><strong>Waste Metrics</strong></p>
		    <div id="graph" class="graph">
      </div>
      
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      
            </div>
            <div class='modal' id='modal'> 
                        <img id='modal-pic' src=''>
                        <p>Item details:</p>
                        <form  class='modal-form' id='modal-form'>
                        <input id='modal_id' name='id' value=''>
                        <!-- the above gets passed to php to identify item in question -->
                        <input id='modal_log_id' name='log_id' value=''>
                            <div>
                                <label for='name'>Name*:</label>
                                <input id='modal_name' name='name' value='' readonly>
                            </div>
                            <div>
                                <label for='quantity'>Quantity:</label>
                                <input id='modal_quantity' name='quantity' type='number' min="0" value=''>
                            </div>
                            <div>
                                <label for='man_date'>Manufacture date:</label>
                                <input id='modal_man_date' name='man_date' type='date' value=''>
                            </div>
                            <div>
                                <label for='exp_date'>Expiry date*:</label>
                                <input id='modal_exp_date' name='exp_date' type='date' value='' required>
                            </div>
                            <div class="fresh_div">
                                <label for="freshness">Freshness*:</label>
                                <p class='zero'>0</p>
                                <input type="range" id="modal_fresh" value="10" name="freshness" min="0" max="10" required onclick="dispFresh(this.id);">
                                <p>10</p>
                                <span class="fresh_tooltip" id="tt1">10</span>
                            </div>
                            <p>*Indicates required fields</p>
                            <div>
                                <button type='submit' id='submit-for-add'>Add</button>
                                <button type='submit' id='submit-for-edit'>Update</button>
                                <button type='button' id ='modal-cancel'>Cancel</button><br><br>
                                <button type='button' id ='modal-out'>Ran out</button>
                            </div>
                        </form>
                    </div>
                    <div class='success-modal' id='add-success'>
                        <p id='success-text'>Item was added!</p>
                    </div>
	</div>
        </body>
</html>

