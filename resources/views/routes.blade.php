<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>List Routes</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    </head>
    <body>
    	<table width="100%" border="1">
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
    </body>
</html>
