<div style="width: 100%; background-color: #ffffff;">
	@foreach( $filters as $filter )
		<div style="padding: 0.75rem 1rem; display: flex; align-items: start; justify-content: space-between; border-bottom: 1px solid #cccccc;">
			<h3 style="margin: 0; font-size: 1rem;">{{ $filter }}</h3>
			<span>&#8964</span>
		</div>
	@endforeach
	<button style="text-transform: uppercase; width: 100%; padding: 0.5rem;">
		Clear all filters
	</button>
</div>
