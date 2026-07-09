import '../css/app.css';
import Alpine from 'alpinejs';

// The `duet-date-picker` custom element is provided by the core Pressbooks
// `duet-date-picker` script handle, declared as a dependency when this bundle
// is enqueued (see PressbooksNetworkCatalog::enqueueScripts).

window.Alpine = Alpine;

const form = document.getElementById( 'network-catalog-form' );

const anchorIdRedirection = '#catalog';

const mobileBreakpoint = 768;

// Keep duet pickers mirrored to real inputs at submit-time; validation will target visible inputs only

form.addEventListener( 'submit', function ( event ) {
	const inputs = Array
		.from( event.target.elements )
		.filter( input => [ 'search', 'pg', 'published_from', 'published_to', 'updated_from', 'updated_to' ].includes( input.name ) );

	// Ensure duet pickers' values are reflected in real inputs so browser validation works
	/**
	 *
	 * @param identifier
	 */
	const mirrorPickerToInput = identifier => {
		// look for an existing input inside the form
		let input = event.target.querySelector( `input[name="${ identifier }"]` );
		const picker = document.querySelector( `duet-date-picker[identifier="${ identifier }"]` );
		const value = picker ? ( picker.getAttribute( 'value' ) || picker.value || '' ) : ( input ? input.value : '' );
		if ( ! input ) {
			// create a hidden input only if none exists (so the form submission contains the value)
			input = document.createElement( 'input' );
			input.type = 'hidden';
			input.name = identifier;
			input.id = identifier;
			input.value = value;
			event.target.appendChild( input );
		} else {
			input.value = value;
		}
	};

	[ 'published_from', 'published_to', 'updated_from', 'updated_to' ].forEach( mirrorPickerToInput );

	// disable pagination when submitting the form since we want to reset it
	inputs
		.filter( input => input.name === 'pg' )
		.forEach( input => input.disabled = true );

	// disable search input that is not visible
	inputs
		.filter( input => input.name === 'search' )
		.filter( input => input.offsetWidth === 0 && input.offsetHeight === 0 )
		.forEach( input => input.disabled = true );

	// disable all inputs that are empty
	inputs
		.filter( input => input.value === '' )
		.forEach( input => input.disabled = true );

	// Ensure that the two date ranges (published and updated) are valid.
	/**
	 *
	 * @param fromName
	 * @param toName
	 */
	const validateDateRange = ( fromName, toName ) => {
		const from = document.querySelector( `input[name="${ fromName }"]` );
		const to = document.querySelector( `input[name="${ toName }"]` );
		if ( ! ( from && to && from.value && to.value ) ) return false; // nothing to validate

		const fromDate = new Date( from.value );
		const toDate = new Date( to.value );
		if ( fromDate > toDate ) {
			// Try to report validity on a visible input so the browser popup appears
			const candidate = document.querySelector( `input[id="${ toName }"], input[name="${ toName }"]` );
			/**
			 *
			 * @param el
			 */
			const isVisible = el => el && el.type !== 'hidden' && el.offsetWidth > 0 && el.offsetHeight > 0 && window.getComputedStyle( el ).visibility !== 'hidden';
			if ( isVisible( candidate ) ) {
				candidate.setCustomValidity( 'The "To" date must be greater than or equal to the "From" date.' );
				try {
					candidate.focus( { preventScroll: true } );
				} catch {
					// Focus may fail in some browsers; ignore
				}
				candidate.reportValidity();
				return true; // validation failed and reported
			}
		}
		return false;
	};

	// Validate published and updated ranges; if either reports a visible validation error, prevent submit
	if ( validateDateRange( 'published_from', 'published_to' ) ) {
		event.preventDefault();
		return false;
	}
	if ( validateDateRange( 'updated_from', 'updated_to' ) ) {
		event.preventDefault();
		return false;
	}

	// disable duplicated filters according to screen size to avoid duplicated parameters
	// this is needed because we have two sets of filters, one for mobile and one for desktop because of design constraints
	const filtersMobile = document.querySelectorAll( '.order-mobile select' );
	const filtersDesktop = document.querySelectorAll( '.order-desktop select' );
	const searchMobile = document.querySelector( '.mobile-bar input' );
	const searchDesktop = document.querySelector( '.results input' );
	if ( window.innerWidth > mobileBreakpoint ) {
		filtersMobile.forEach( el => {
			el.disabled = true;
		} );
		searchMobile.disabled = true;
	} else {
		filtersDesktop.forEach( el => {
			el.disabled = true;
		} );
		searchDesktop.disabled = true;
	}

	return true;
} );

// Attach the same duetChange handler to both published_to and updated_to date-pickers
[ 'updated_to', 'published_to' ].forEach( identifier => {
	const picker = document.querySelector( `duet-date-picker[identifier="${ identifier }"]` );
	if ( ! picker ) return;
	picker.addEventListener( 'duetChange', () => {
		const targetInput = document.querySelector( `input[name="${ identifier }"], #${ identifier }` );
		if ( ! targetInput ) return;
		// Clear custom validity so browser validation UI is removed
		targetInput.setCustomValidity( '' );
		// Notify other code that the underlying input changed
		targetInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
	} );
} );

/**
 *
 */
window.submitForm = () => {
	document.getElementById( 'apply-filters' ).click();
};

// Toggle the "open" class on the hamburger menu
const headerToggle = document.querySelector( '.js-header-nav-toggle' );
if ( headerToggle ) {
	const headerNav = document.querySelector( '.header__nav' );
	headerToggle.addEventListener( 'click', () => {
		if ( headerNav ) headerNav.classList.toggle( 'header__nav--active' );
	} );
}

const pgElements = document.querySelectorAll( '[name="pg"]' );
pgElements.forEach( element => {
	element.addEventListener( 'change', function ( event ) {
		const pageRegex = /pg=\d+/;
		const pageParam = `pg=${ event.target.value }`;
		const currentSearch = window.location.search;
		const url = window.location.href.split( '?' )[0];

		if ( currentSearch.match( pageRegex ) ) {
			window.location.href = `${ url }${ currentSearch.replace( pageRegex, pageParam ) }${ anchorIdRedirection }`;

			return;
		}

		if ( ! currentSearch ) {
			window.location.href = `${ url }?${ pageParam }${ anchorIdRedirection }`;

			return;
		}

		window.location.href = `${ url }${ currentSearch }&${ pageParam }${ anchorIdRedirection }`;
	} );
} );

/**
 *
 * @param root0
 * @param root0.open
 * @param root0.items
 * @param root0.selected
 */
window.selectableFilters = ( { open, items, selected } ) => {
	return {
		open,
		items,
		selected,
		search: '',
		displayAmount: 10,
		/**
		 *
		 */
		toggle() {
			this.open = ! this.open;
		},
		/**
		 *
		 */
		empty() {
			return this.filteredItems().length === 0;
		},
		/**
		 *
		 */
		filteredItems() {
			return Object.entries( this.items )
				.filter(
					( [ , value ] ) => value && value.toLowerCase().includes( this.search.toLowerCase() )
				).slice( 0, this.displayAmount );
		},
		/**
		 *
		 */
		showMore() {
			this.displayAmount += 10;
		},
		/**
		 *
		 * @param value
		 */
		highlightSearch( value ) {
			if ( ! this.search ) {
				return value;
			}

			return value.replaceAll(
				new RegExp( `(${ this.search.toLowerCase() })`, 'ig' ),
				'<span class="font-bold">$1</span>'
			);
		},
	};
};

/**
 *
 * @param event
 */
window.changeOnSelect = event => {
	if ( event.target.closest( '.order-mobile' ) ) { //disable desktop/mobile select to avoid duplicated parameters
		const select = document.querySelector( '.order-desktop select' );
		select.disabled = true;
	} else {
		const select = document.querySelector( '.order-mobile select' );
		select.disabled = true;
	}
	window.submitForm();
};

/**
 *
 * @param element
 */
window.hasClampedText = element => {
	return element.offsetHeight < element.scrollHeight || element.offsetWidth < element.scrollWidth;
};

/**
 *
 * @param element
 * @param className
 */
window.toggleClass = ( element, className ) => {
	element.classList.toggle( className );
};

/**
 *
 * @param filter
 */
window.removeFilter = filter => {
	if ( ! filter ) return;
	const attr = [ 'h5p' ].includes( filter ) ? 'name' : 'value';

	if ( filter.endsWith( '_from' ) || filter.endsWith( '_to' ) ) {
		// clear the duet picker by identifier (mirrorPickerToInput looks for identifier)
		const identifier = filter;
		const dp = document.querySelector( `duet-date-picker[identifier="${ identifier }"], duet-date-picker[name="${ identifier }"]` );
		if ( dp ) {
			try {
				dp.setAttribute( 'value', '' );
			} catch {
				// setAttribute may fail; ignore
			}
			try {
				dp.value = '';
			} catch {
				// Value assignment may fail; ignore
			}
			dp.dispatchEvent( new Event( 'duetChange', { bubbles: true } ) );
		}
		// remove any underlying native input so the submit-time mirror won't repopulate the old value
		const hiddenByName = document.querySelector( `input[name="${ identifier }"]` );
		if ( hiddenByName ) hiddenByName.remove();
		const hiddenById = document.querySelector( `input[id="${ identifier }"]` );
		if ( hiddenById ) hiddenById.remove();
		// delay submit slightly to let duet update its internals before mirror runs
		if ( typeof window.submitForm === 'function' ) setTimeout( window.submitForm, 80 );
		return;
	} else {
		const el = document.querySelector( `input[${ attr }="${ filter }"]` );
		if ( el ) el.click();
	}
	// non-date filters submit immediately
	if ( typeof window.submitForm === 'function' ) window.submitForm();
};

/**
 *
 */
window.reset = () => {
	document.getElementById( 'network-catalog-form' ).reset();
	window.location.href = window.location.href.split( '?' )[0] + `${ anchorIdRedirection }`;
};

Alpine.start();
