@extends('master_template')

@section('body')
<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="packyakNavContainer">
			<div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">PackYak</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="page-scroll">
                        <a href="#">About</a>
                    </li>
                    <li class="page-scroll">
                        <a href="{{ url('/auth/login') }}">Login</a>
                    </li>
                    <li class="page-scroll">
                        <a href="{{ url('/auth/register') }}">Register</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->

		</div>
	</nav>

</body>
@endsection