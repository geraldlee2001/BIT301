// Function to generate QR code for seat viewing
function generateSeatViewQR ( productId, bookingId )
{
  // Create a container for the QR code
  const qrContainer = document.createElement( 'div' );
  qrContainer.id = `qrCodeContainer_${ bookingId }`;
  qrContainer.style.cssText = 'text-align: center; margin: 20px 0; display: none;';

  // Create a heading for the QR code section
  const heading = document.createElement( 'h4' );
  heading.textContent = 'Ticket Information';
  heading.style.marginBottom = '10px';
  qrContainer.appendChild( heading );

  // Create a div for the QR code
  const qrElement = document.createElement( 'div' );
  qrElement.id = `qrCode_${ bookingId }`;
  qrContainer.appendChild( qrElement );

  // Get event details from the card
  const cardBody = document.querySelector( `[data-booking-id="${ bookingId }"]` );
  if ( !cardBody )
  {
    console.error( 'Card body not found for booking:', bookingId );
    const errorMessage = document.createElement( 'p' );
    errorMessage.textContent = 'Error: Booking information not found.';
    errorMessage.style.color = 'red';
    qrContainer.appendChild( errorMessage );
    return qrContainer;
  }

  // Safely get event details with null checks
  const eventNameElement = cardBody.querySelector( '.fw-bold' );
  const eventDateElement = document.getElementById( `eventDate` );
  const seatsElement = cardBody.querySelector( '.text-muted' );

  let missingFields = [];
  if ( !eventNameElement ) missingFields.push( 'Event Name' );
  if ( !eventDateElement ) missingFields.push( 'Event Date' );
  if ( !seatsElement ) missingFields.push( 'Seat Information' );

  if ( missingFields.length > 0 )
  {
    console.error( 'Missing required fields:', missingFields.join( ', ' ) );
    const errorMessage = document.createElement( 'p' );
    errorMessage.textContent = `Error: Missing required information (${ missingFields.join( ', ' ) })`;
    errorMessage.style.color = 'red';
    qrContainer.appendChild( errorMessage );
    return qrContainer;
  }

  const eventName = eventNameElement.textContent.trim();
  const eventDate = eventDateElement.textContent.replace( 'Event Date: ', '' ).trim();
  const seats = seatsElement.textContent.replace( 'Seats: ', '' ).trim();

  // Validate data is not empty
  if ( !eventName || !eventDate || !seats )
  {
    console.error( 'One or more required fields are empty' );
    const errorMessage = document.createElement( 'p' );
    errorMessage.textContent = 'Error: Required ticket information is incomplete.';
    errorMessage.style.color = 'red';
    qrContainer.appendChild( errorMessage );
    return qrContainer;
  }

  // Create the ticket information message
  const ticketInfo = `Event: ${ eventName }\nDate: ${ eventDate }\nSeats: ${ seats }`;
  console.log( 'Generated ticket info:', ticketInfo );

  try
  {
    // Create new QR Code instance with the ticket information
    new QRCode( qrElement, {
      text: ticketInfo,
      width: 128,
      height: 128,
      colorDark: '#000000',
      colorLight: '#ffffff',
      correctLevel: QRCode.CorrectLevel.H
    } );

    // Add a caption below the QR code
    const caption = document.createElement( 'p' );
    caption.textContent = 'Scan to view ticket information';
    caption.style.cssText = 'margin-top: 10px; font-size: 0.9em; color: #666;';
    qrContainer.appendChild( caption );
  } catch ( error )
  {
    console.error( 'Failed to generate QR code:', error );
    const errorMessage = document.createElement( 'p' );
    errorMessage.textContent = 'Error: Failed to generate QR code. Please try again.';
    errorMessage.style.color = 'red';
    qrContainer.appendChild( errorMessage );
  }

  return qrContainer;
}

// Function to toggle QR code display
function toggleQRCode ( productId, bookingId )
{
  const cardBody = document.querySelector( `[data-booking-id="${ bookingId }"]` );
  if ( !cardBody ) return;

  // Check if booking is cancelled
  const cancelledBadge = cardBody.querySelector( '.badge-danger' );
  if ( cancelledBadge && cancelledBadge.textContent === 'CANCELLED' )
  {
    alert( 'Cannot display QR code for cancelled bookings.' );
    return;
  }

  const existingContainer = document.getElementById( `qrCodeContainer_${ bookingId }` );
  if ( existingContainer )
  {
    existingContainer.style.display = existingContainer.style.display === 'none' ? 'block' : 'none';
    return;
  }

  const qrContainer = generateSeatViewQR( productId, bookingId );

  if ( cardBody && qrContainer )
  {
    cardBody.appendChild( qrContainer );
    qrContainer.style.display = 'block';
  } else
  {
    alert( 'Unable to generate QR code. Some ticket information is missing.' );
  }
}