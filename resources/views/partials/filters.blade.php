@foreach( $filters as $filter )
	<div class="side-filter">
		<h3>{{ $filter }}</h3>
		<span>&#8964</span>
	</div>
@endforeach
<button class="reset-filters">
	{{ __('Clear filters', 'pressbooks-network-catalog') }}
</button>
