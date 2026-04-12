//wait until html fully loads so it can find eventscontainer
document.addEventListener("DOMContentLoaded", function() {
    loadEvents();
});


//get events from db and display them on page
async function loadEvents() {
    let response = await fetch("getEvents.php");
    let data = await response.json();
    console.log(data);

    let eventsContainer = document.getElementById("eventsContainer");
    eventsContainer.innerHTML = ""; 

    data.forEach(function(event){
        eventsContainer.innerHTML += `
            <div class="event">
                <h3>${event.title}</h3>
                <p>${event.description}</p>
                <p><b>Date:</b> ${event.event_date}</p>
                <p><b>Time:</b> ${event.event_time}</p>
                <p><b>Location:</b> ${event.location}</p>

                <button id="rsvpBtn" onclick="rsvp(${event.event_id})">RSVP</button> 
                <span id="count-${event.event_id}">${event.rsvp_count || 0} going </span>
            
                </div>
        `;

    });

}


//send rsvp to server and save to db 
async function rsvp(eventId) {
    try {
        let response = await fetch("rsvp.php", {
            method: "POST",
            headers: {
                //tell server we're sending form data
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "event_id=" + eventId
        });

        let result = await response.text();

        alert("RSVP saved!");
        loadEvents();

    } catch (err) {
        console.error("RSVP error:", err);
        alert("Something went wrong.");
    }

}