function addToCart ()
{
  const selectedSeats = Array.from( document.querySelectorAll( '.seat.selected' ) )
    .map( seat => `${ seat.getAttribute( "data-row" ) }-${ seat.textContent.trim() }` );

  if ( selectedSeats.length === 0 )
  {
    alert( "Please select at least one seat." );
    return;
  }

  fetch( '/php/addToCart.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify( {
      productId,
      seats: selectedSeats
    } )
  } )
    .then( res => res.json() )
    .then( data =>
    {
      if ( data.success )
      {
        alert( "Seats added to cart!" );
        location.reload();
      } else
      {
        alert( "Failed to add to cart." );
      }
    } )
    .catch( err =>
    {
      console.error( "Error adding to cart:", err );
    } );
}
