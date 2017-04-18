<?php
use Tour\Board;
use Tour\Knight;

require_once dirname(__FILE__) . '/../vendor/autoload.php';

$x = $y = 0;
$size = 8;
$error = false;
$classes=[];
$result = [
    'moves' => 0,
    'counter' => 0,
    'total' => pow($size, 2),
    'history' => [],
    'coverage' => 0
];
if (! empty($_POST)) {
    $classes[] = 'post';
    $loc = [
        0,
        0
    ];
    $size = 8;
    if (! empty($_POST['x']) && ! empty($_POST['y'])) {
        $loc = [
            $x = $_POST['x'],
            $y = $_POST['y']
        ];
    }
    if (! empty($_POST['size'])) {
        $size = $_POST['size'];
    }
    
    $board = new Board($loc, $size);
    $knight = new Knight($board);
    try {
        $knight->explore(pow($size, 2) * 2);
        $result['moves'] = $knight->getNumMoves();
        $result['counter'] = $board->getCounter();
        $result['history'] = $knight->getHistory();
        $result['total'] = pow($size, 2);
        $result['coverage'] = $result['counter'] / $result['total'];
    } catch (\Exception $e) {
        $error = $e->getMessage();
        $classes[] = 'error';
    }
}
echo "<script>var data=" . json_encode($result) . ";</script>";
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">

<title>Knight's Tour</title>

<!-- Bootstrap Core CSS -->
<link href="css/app.css" rel="stylesheet">

<!-- Custom CSS -->
<link href="css/bootstrap-slider.min.css" rel="stylesheet">

<style>
body {
	padding-top: 70px;
	/* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
}
</style>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="<?php echo implode(" ", $classes); ?>">

	<!-- Navigation -->
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span
						class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Knight's Tour</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse"
				id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li><a href="#">About</a></li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container -->
	</nav>

	<!-- Page Content -->
	<div class="container">
		<?php if ($error) : ?>
            <div class="alert alert-danger">
              <strong>Error: </strong> <?php echo $error; ?>
            </div>
		<?php endif; ?>

		<div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
            	<div class="well well-sm" id="results">
            		<div class="row">
            			<div class="col-xs-12">
            				<h1>Results</h1>
            				<div class="row">
            					<div class="col-xs-6">
                					<p><span class="coverage-num"><?php echo $result['coverage'] * 100; ?>%</span> of squares covered</p>
        							<p>Algorithm efficiency is : <?php echo max(0, 100 - round(((($result['moves'] - $result['total'])) / $result['total']) * 100, 2)); ?>%</p>
                				</div>
                				<div class="col-xs-6">
                					<p><span class="moves-num">Tour completed in <?php echo $result['moves']; ?></span> moves</p>
        							<p>Extra Squares Used : <?php echo max(0,$result['moves'] - $result['total']); ?></p>
                				</div>
            				</div>
            			</div>
            		</div>
            		<div style="width: 100%; clear: both;"></div>
            	</div>
            	<div style="width:100%; margin-bottom: 15px;"></div>
            </div>
		</div>
		<!-- /.row -->
		
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3 text-center">
				<input id="slider" data-slider-id='tourSlider' type="text" data-slider-min="0" data-slider-max="<?php echo $result['moves']; ?>" data-slider-step="1" data-slider-value="0"/>
			</div>
		</div>
		<!-- /.row -->
		
		<div class="row">
			<div class="col-xs-12 col-md-offset-3 col-md-6 text-center">
				<ul id="board">
                <?php for ($ix = 0; $ix < $size; $ix ++) : ?>
                	<li class="board_row_wrap">
                    	<ul class="board_row" id="<?php echo "row_{$ix}"; ?>">
                    	<?php for ($iy = 0; $iy < $size; $iy ++) : ?>
                    		<li class="board_square" id="<?php echo "{$ix}x{$iy}"; ?>"></li>
                    	<?php endfor; ?>
                    	</ul>
                    </li>
                <?php endfor; ?>
				</ul>
			</div>
		</div>
		<!-- /.row -->

		<div class="row">
			<div class="col-md-6 col-md-offset-3 col-xs-12 text-center">
				<form class="form-inline" name="ktform" id="ktform" method="post">

					<div class="form-group">
						<!-- X field -->
						<label class="sr-only " for="x">Location (x axis)</label> <input
							class="form-control" id="x" name="x" type="text"
							placeholder="loc (x)" value="<?php echo $x ?: ''; ?>" />
					</div>

					<div class="form-group">
						<!-- Y field -->
						<label class="sr-only " for="y">Location (y axis)</label> <input
							class="form-control" id="y" name="y" type="text"
							placeholder="loc (y)" value="<?php echo $y ?: ''; ?>" />
					</div>

					<div class="form-group">
						<!-- Size field -->
						<label class="sr-only" for=size>Size</label> <input
							class="form-control" id="size" name="size" type="text"
							placeholder="squares &sup2;"
							value="<?php echo $size != 8 ? $size : ''; ?>" />
					</div>

					<div class="form-group">
						<button class="btn btn-primary " name="submit" type="submit">Submit</button>
					</div>

				</form>
			</div>
		</div>
		<!-- /.row -->

	</div>
	<!-- /.container -->

	<img src="img/knight.svg" alt="Chess Knight" id="knight" />
	
	<!-- jQuery Version 1.11.1 -->
	<script src="js/jquery.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.min.js"></script>
	<script src="js/bootstrap-slider.min.js"></script>

	<!-- App JavaScript -->
	<script src="js/app.js"></script>
</body>

</html>
