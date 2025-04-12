function checkWaitingListStatus ( productId )
{
  fetch( `php/check_waiting_list_status.php?productId=${ productId }` )
    .then( response => response.json() )
    .then( data =>
    {
      if ( data.success && data.isOnWaitingList )
      {
        const waitingListButton = document.getElementById( 'waitingListButton' );
        if ( waitingListButton )
        {
          waitingListButton.disabled = true;
          waitingListButton.textContent = 'On Waiting List';
        }
      }
    } )
    .catch( error => console.error( 'Error:', error ) );
}

function checkEventAvailability ( productId )
{
  fetch( `php/check_product_availability.php?id=${ productId }` )
    .then( response => response.json() )
    .then( data =>
    {
      const bookButton = document.getElementById( 'bookButton' );
      const waitingListButton = document.getElementById( 'waitingListButton' );

      if ( data.available === false )
      {
        bookButton.style.display = 'none';
        waitingListButton.style.display = '';
        checkWaitingListStatus( productId );
      } else
      {
        bookButton.style.display = '';
        waitingListButton.style.display = 'none';
      }
    } )
    .catch( error => console.error( 'Error:', error ) );
}

function showContactForm ( productId )
{
  const contactForm = document.createElement( 'div' );
  contactForm.innerHTML = `
    <div class="modal" id="contactFormModal" style="display:block; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
      <div style="color:black; background:white; padding:20px; max-width:500px; margin:50px auto; border-radius:5px;">
        <h3>Contact Details</h3>
        <form id="waitingListForm">
          <div style="margin-bottom:15px;">
            <label>Contact details:</label>
            <input type="tel" id="phone" placeholder="Phone Number" required style="width:100%; padding:8px; margin-top:5px;">
          </div>
          <div style="margin-bottom:15px;">
            <input type="email" id="email" placeholder="Email Address" style="width:100%; padding:8px; margin-top:5px;">
          </div>
          <div style="margin-bottom:15px;">
            <label>Preferred Contact Method:</label>
            <div style="display:flex; gap:15px; margin-top:5px;">
              <label style="display:flex; align-items:center;">
                <input type="radio" name="preferredContact" value="PHONE" checked> Phone
              </label>
              <label style="display:flex; align-items:center;">
                <input type="radio" name="preferredContact" value="EMAIL"> Email
              </label>
              <label style="display:flex; align-items:center;">
                <input type="radio" name="preferredContact" value="BOTH"> Both
              </label>
            </div>
          </div>
          <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button" onclick="closeContactForm()" style="padding:8px 15px;">Cancel</button>
            <button type="submit" style="padding:8px 15px; background:#0d6efd; color:white; border:none; border-radius:4px;">Join Waiting List</button>
          </div>
        </form>
      </div>
    </div>
  `;
  document.body.appendChild( contactForm );

  // Add event listener for email field visibility based on contact preference
  const preferredContactRadios = document.querySelectorAll( 'input[name="preferredContact"]' );
  const emailField = document.getElementById( 'email' );

  preferredContactRadios.forEach( radio =>
  {
    radio.addEventListener( 'change', function ()
    {
      if ( this.value === 'EMAIL' || this.value === 'BOTH' )
      {
        emailField.required = true;
      } else
      {
        emailField.required = false;
      }
    } );
  } );

  document.getElementById( 'waitingListForm' ).addEventListener( 'submit', function ( e )
  {
    e.preventDefault();
    const phone = document.getElementById( 'phone' ).value;
    const email = document.getElementById( 'email' ).value;
    const preferredContact = document.querySelector( 'input[name="preferredContact"]:checked' ).value;
    joinWaitingList( productId, phone, email, preferredContact );
  } );
}

function closeContactForm ()
{
  const modal = document.getElementById( 'contactFormModal' );
  if ( modal )
  {
    modal.parentElement.remove();
  }
}

function joinWaitingList ( productId, phone, email, preferredContact )
{
  fetch( 'php/add_to_waiting_list.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `productId=${ productId }&phone=${ phone }&email=${ email }&preferredContact=${ preferredContact }`
  } )
    .then( async response =>
    {
      console.log( 'Response status:', response );
      const data = await response.json();
      if ( !response.ok )
      {
        if ( response.status === 401 )
        {
          throw new Error( 'Please log in to join the waiting list' );
        }
        throw new Error( data.error || 'Failed to join waiting list' );
      }
      return data;
    } )
    .then( data =>
    {
      if ( data.success )
      {
        closeContactForm();
        const successMessage = data.message || 'Successfully joined the waiting list!';
        const notification = document.createElement( 'div' );
        notification.style.cssText = 'position:fixed;top:20px;right:20px;background:#28a745;color:white;padding:15px;border-radius:5px;z-index:1000;';
        notification.textContent = successMessage;
        document.body.appendChild( notification );
        setTimeout( () => notification.remove(), 3000 );

        const waitingListButton = document.getElementById( 'waitingListButton' );
        waitingListButton.disabled = true;
        waitingListButton.textContent = 'On Waiting List';
      }
    } )
    .catch( error =>
    {
      console.error( 'Error:', error );
      const errorMessage = error.message || 'An error occurred while joining the waiting list';
      const notification = document.createElement( 'div' );
      notification.style.cssText = 'position:fixed;top:20px;right:20px;background:#dc3545;color:white;padding:15px;border-radius:5px;z-index:1000;';
      notification.textContent = errorMessage;
      document.body.appendChild( notification );
      setTimeout( () => notification.remove(), 3000 );
    } );
}

// For testing purposes
function simulateAvailability ( productId )
{
  fetch( 'php/notify_waiting_list.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `productId=${ productId }`
  } )
    .then( response => response.json() )
    .then( data =>
    {
      if ( data.success )
      {
        alert( 'Notification sent to waiting list!' );
      } else
      {
        alert( data.error || 'Failed to send notification' );
      }
    } )
    .catch( error =>
    {
      console.error( 'Error:', error );
      alert( 'An error occurred while sending notification' );
    } );
}