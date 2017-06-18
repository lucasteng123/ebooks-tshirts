<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="tshirts.css">
	<title>T-Shirt Update</title>
</head>
<body>
<div class="container">
	<div class="row tshirt-container">
		<?php echo file_get_contents("http://127.0.0.1/Best_E_Books_TShirt/tshirt-list")?>
	</div>

	<div class="row">
	<hr>
		<div class="col-md-3"><h2 style="text-align: right;vertical-align: center;">New T-Shirt</h2></div>
		<div class="col-md-9">
			<div class="col-md-4"></div>
			<div class="col-md-8"></div>

			<form action="/tshirt-update"  method="post">

				<div class="form-group">
				<label for="image"> Path to Image </label>
				<div class="input-group">
					
					<input type="text" class="form-control" name="image">
					<span class="input-group-btn">
        				<button class="btn btn-default" type="button">Browse</button>
      				</span>
				</div>
				</div>
				<div class="form-group">
					<label for="name"> Name for T-Shirt </label>
					<input type="text" class="form-control" name="name">
				</div>
				<div class="form-group">
					<label for="name"> Description </label>
					<input type="text" class="form-control" name="description">
				</div>
				<div class="form-group">
					<label for="colors"> Desired Colors (comma seperated) </label>
					<input type="text" class="form-control" name="colors">
				</div>
				<div class="form-group">
					<label for="colors"> Desired Sizes (comma seperated) </label>
					<input type="text" class="form-control" name="size">
				</div>
				<div class="input-group" style="padding:10px 0px 30px 0px">
					
					<span class="input-group-addon">$</span>
					<input type="text" class="form-control" name="price">
				</div>
				<div class="form-group">
					<input class="btn btn-primary" type="submit">
				</div>
			</form>	
		</div>				
	</div>			
</div>
</body>
</html>