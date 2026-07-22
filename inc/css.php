 <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/theme.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
	<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
	<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

	<style type="text/css">
		em{
			color: red;
		}
		.loader-main{
			width: 100%;
		    z-index: 99999;
		    position: fixed;
		    left: 0;
		    right: 0;
		    top: 0;
		    display: block;
		    margin: 0 auto;
		    height: 100vh;
		    background-color: rgba(0,0,0,0.85);
		}
		
		#loader {
			left: 0;
		    right: 0;
		    display: block;
		    margin: 0 auto;
			position: absolute;
		  	top: 41%;
		 	z-index: 1;
		   	border: 16px solid #f3f3f3;
		  	border-radius: 50%;
		  	border-top: 16px solid blue;
		  	border-right: 16px solid green;
		  	border-bottom: 16px solid red;
		  	border-left: 16px solid pink;
		  	width: 120px;
		  	height: 120px;
		  	-webkit-animation: spin 2s linear infinite;
		  	animation: spin 2s linear infinite;
		}

		@-webkit-keyframes spin {
		  0% { -webkit-transform: rotate(0deg); }
		  100% { -webkit-transform: rotate(360deg); }
		}

		@keyframes spin {
		  0% { transform: rotate(0deg); }
		  100% { transform: rotate(360deg); }
		}
	</style>