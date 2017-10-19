<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>List Routes</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{!! asset('css/bootstrap.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('css/jquery.dataTables.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('css/dataTables.bootstrap.min.css') !!}">
    </head>
    <body>
        <div class="page-header">
            <h1 class="text-center">List Routes</h1>
        </div>
        <div class="container">
            <div class="table">
            	<table id="route-table" class="table-bordered table-condensed table-responsive">
            		<thead>
        	    		<tr>
        	    			<th>Method</th>
        	    			<th>URI</th>
        	    			<th>Name</th>
        	    			<th>Action</th>
        	    			<th>Middleware</th>
        	    		</tr>
            		</thead>
            		<tbody>
            			@foreach ($routeCollection as $route)
        	    			<tr>
        	    				<td>{!! implode('|', $route->methods()) !!}</td>
        	    				<td>{!! $route->uri() !!}</td>
        	    				<td>{!! $route->getName() !!}</td>
        	    				<td>{!! $route->getActionName() !!}</td>
        	    				<td>
        	    					{!! collect($route->gatherMiddleware())->map(function ($middleware) {
                    						return $middleware instanceof \Closure ? 'Closure' : $middleware;
                					})->implode(',') !!}
                				</td>
        	    			</tr>
            			@endforeach
            		</tbody>
            	</table>
            </div>
        </div>

    <script type="text/javascript" src="{!! asset('js/jquery.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/bootstrap.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/jquery.dataTables.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/dataTables.bootstrap.js') !!}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#route-table').dataTable({
                lengthMenu: [
                    [ 50, -1 ],
                    [ '50', 'All' ]
                ],
            });
        });
    </script>
    </body>
</html>
