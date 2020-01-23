<?php
    include'process.php';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel='stylesheet' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
    <link rel='stylesheet' href='https://raw.githubusercontent.com/kartik-v/bootstrap-star-rating/master/css/star-rating.min.css'>
</head>
<body>
<div class="row container">
<div class="col-md-4 ">
    <h3><b>Rating & Reviews</b></h3>
    <div class="row">
    
        <div class="col-md-6">
            <h3 align="center"><b><?php echo round($AVGRATE,1);?></b> <i class="fa fa-star" data-rating="2" style="font-size:20px;color:#ff9f00;"></i></h3>
            <p><?=$Total;?> ratings and <?=$Total_review;?> reviews</p>
        </div>
        <div class="col-md-6">
            <?php
            while($db_rating= mysqli_fetch_array($rating)){
            ?>
                <h4 align="center"><?=$db_rating['rating'];?> <i class="fa fa-star" data-rating="2" style="font-size:20px;color:green;"></i> Total <?=$db_rating['Total'];?></h4>
                
                
            <?php   
            }
                
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"> 
        <?php
            while($db_review= mysqli_fetch_array($review)){
        ?>
                <h4><?=$db_review['rating'];?> <i class="fa fa-star" data-rating="2" style="font-size:20px;color:green;"></i> by <span style="font-size:14px;"><?=$db_review['email'];?></span></h4>
                <p><?=$db_review['remark'];?></p>
                <hr>
        <?php   
            }
                
        ?>
        </div>
    </div>
        
    
    <div id="rating_div">
                <div class="star-rating">
                    <span class="fa divya fa-star-o" data-rating="1" style="font-size:20px;"></span>
                    <span class="fa fa-star-o" data-rating="2" style="font-size:20px;"></span>
                    <span class="fa fa-star-o" data-rating="3" style="font-size:20px;"></span>
                    <span class="fa fa-star-o" data-rating="4" style="font-size:20px;"></span>
                    <span class="fa fa-star-o" data-rating="5" style="font-size:20px;"></span>
                    <input type="hidden" name="whatever3" class="rating-value" value="1">
                </div>
    </div>
</div>
</div><br>
<input type="hidden" name="demo_id" id="demo_id" value="1">
<div class="col-md-4">
<input type="text" class="form-control" name="email" id="email" placeholder="Email Id"><br>
<textarea class="form-control" rows="5" placeholder="Write your review here..." name="remark" id="remark" required></textarea><br>
<p><button  class="btn btn-default btn-sm btn-info" id="srr_rating">Submit</button></p>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="js/index.js"></script>

</body>
</html>