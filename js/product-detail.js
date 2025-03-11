const seatMapContainer = document.getElementById( "seat-map" );

const rows = [
  "A", "B", "C", "D", "E", "F", "G",
  "H", "I", "J", "K", "L", "AA", "BB", "CC", "DD", "EE"
];

const seatCounts = {
  A: 34, B: 34, C: 34, D: 34, E: 34, F: 32, G: 32,
  H: 32, I: 32, J: 32, K: 32, L: 32,
  AA: 50, BB: 50, CC: 50, DD: 50, EE: 25,
};

let bookedSeats = [];

// Get product ID from URL
const urlParams = new URLSearchParams( window.location.search );
const productId = urlParams.get( "id" );

fetch( `/php/getBookedSeats.php?id=${ productId }` )
  .then( res => res.json() )
  .then( data =>
  {
    bookedSeats = data.bookedSeats || [];
    renderSeatMap();
  } );

function renderSeatMap ()
{
  rows.forEach( row =>
  {
    const rowEl = document.createElement( "div" );
    rowEl.classList.add( "row" );

    const label = document.createElement( "span" );
    label.textContent = row;
    label.classList.add( "row-label" );
    rowEl.appendChild( label );

    const count = seatCounts[ row ] || 30;
    for ( let i = 1; i <= count; i++ )
    {
      const seat = document.createElement( "div" );
      seat.classList.add( "seat" );
      seat.setAttribute( "data-row", row );
      seat.textContent = i;

      const seatId = `${ row }-${ i }`;
      if ( bookedSeats.includes( seatId ) )
      {
        seat.classList.add( "booked" );
      } else
      {
        seat.addEventListener( "click", () =>
        {
          seat.classList.toggle( "selected" );
          updateSelectedSeats();
        } );
      }

      rowEl.appendChild( seat );
    }

    seatMapContainer.appendChild( rowEl );
  } );
}

function updateSelectedSeats ()
{
  const selected = document.querySelectorAll( ".seat.selected:not(.booked)" );
  const selectedText = [ ...selected ]
    .map( seat => `${ seat.getAttribute( "data-row" ) }-${ seat.textContent.trim() }` )
    .join( ", " ) || "None";
  document.getElementById( "selected-seats" ).textContent = selectedText;
}
