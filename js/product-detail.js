document.addEventListener( 'DOMContentLoaded', function ()
{
  const seatMap = document.getElementById( 'seat-map' );
  const selectedSeatsSpan = document.getElementById( 'selected-seats' );
  const selectedSeatsInput = document.getElementById( 'selectedSeatsInput' );
  const totalPriceSpan = document.getElementById( 'totalPrice' );
  const promoCodeInput = document.getElementById( 'promoCode' );
  const applyPromoButton = document.getElementById( 'applyPromoCode' );
  const promoMessage = document.getElementById( 'promoMessage' );
  const finalPriceSection = document.querySelector( '.final-price-section' );
  const originalPriceSpan = document.getElementById( 'originalPrice' );
  const discountAmountSpan = document.getElementById( 'discountAmount' );
  const finalPriceSpan = document.getElementById( 'finalPrice' );
  const appliedDiscountInput = document.getElementById( 'appliedDiscount' );

  let selectedSeats = new Map(); // Map to store seat IDs and their details
  let appliedDiscount = 0;

  // Handle seat selection
  seatMap.addEventListener( 'click', function ( e )
  {
    const seat = e.target.closest( '.seat' );
    if ( !seat || seat.classList.contains( 'booked' ) ) return;

    const seatId = seat.dataset.seat;
    const seatPrice = parseFloat( seat.dataset.price || 0 );
    const seatType = seat.dataset.type || '';

    if ( selectedSeats.has( seatId ) )
    {
      selectedSeats.delete( seatId );
      seat.classList.remove( 'selected' );
    } else
    {
      selectedSeats.set( seatId, {price: seatPrice, type: seatType} );
      seat.classList.add( 'selected' );
    }

    updateSelectedSeatsDisplay();
    updatePriceDisplay();
  } );

  function updateSelectedSeatsDisplay ()
  {
    const seatDetails = Array.from( selectedSeats.entries() ).map( ( [ seatId, details ] ) =>
      `${ seatId } (${ details.type })`
    );
    selectedSeatsSpan.textContent = seatDetails.length > 0 ? seatDetails.join( ', ' ) : 'None';
    selectedSeatsInput.value = Array.from( selectedSeats.keys() ).join( ',' );
  }

  function updatePriceDisplay ()
  {
    const subtotal = Array.from( selectedSeats.values() )
      .reduce( ( sum, details ) => sum + details.price, 0 );
    const discount = subtotal * ( appliedDiscount / 100 );
    const finalPrice = subtotal - discount;

    totalPriceSpan.textContent = `RM${ subtotal.toFixed( 2 ) }`;

    if ( appliedDiscount > 0 )
    {
      originalPriceSpan.textContent = `RM${ subtotal.toFixed( 2 ) }`;
      discountAmountSpan.textContent = `-RM${ discount.toFixed( 2 ) }`;
      finalPriceSpan.textContent = `RM${ finalPrice.toFixed( 2 ) }`;
    }
  }

  // Handle promo code application
  applyPromoButton.addEventListener( 'click', function ()
  {
    const promoCode = promoCodeInput.value.trim();
    if ( !promoCode )
    {
      promoMessage.textContent = 'Please enter a promo code';
      return;
    }
    const subtotal = Array.from( selectedSeats.values() )
      .reduce( ( sum, details ) => sum + details.price, 0 );
    const eventId = new URLSearchParams( window.location.search ).get( 'id' );
    console.log( `promoCode=${ encodeURIComponent( promoCode ) }&productId=${ encodeURIComponent( eventId ) }&totalPrice=${ encodeURIComponent( subtotal ) }` )
    // Make API call to check promo code
    fetch( '/php/apply_promo_code.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `code=${ encodeURIComponent( promoCode ) }&productId=${ encodeURIComponent( eventId ) }&price=${ encodeURIComponent( subtotal ) }`
    } )
      .then( response => response.json() )
      .then( data =>
      {
        if ( data.success )
        {
          const discountAmount = data.discount_value;
          const originalPrice = data.original_price;
          appliedDiscount = ( ( discountAmount / originalPrice ) * 100 ).toFixed( 0 );
          appliedDiscountInput.value = appliedDiscount;
          promoMessage.textContent = `Promo code applied! ${ appliedDiscount }% discount`;
          promoMessage.style.color = 'green';
          finalPriceSection.style.display = 'block';
          updatePriceDisplay();
        } else
        {
          promoMessage.textContent = data.message || 'Invalid promo code';
          promoMessage.style.color = 'red';
          appliedDiscount = 0;
          appliedDiscountInput.value = '';
          finalPriceSection.style.display = 'none';
        }
      } )
      .catch( error =>
      {
        console.error( 'Error:', error );
        promoMessage.textContent = 'Error applying promo code';
        promoMessage.style.color = 'red';
      } );
  } );
} );

