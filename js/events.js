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

    //loop through events and create it, then add to page 
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
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "event_id=" + eventId
        });

        let result = await response.text();

        if (result.trim() === "success") {
            alert("RSVP saved!");
            loadEvents(); // Refresh the list to see the new count
        } else {
            alert("Server error: " + result);
        }
    } catch (err) {
        console.error("RSVP error:", err);
    }
}

//this shows the form when user clicks create event, otherwise is hidden 
function toggleCreateForm() {
    let form = document.getElementById("createEventForm");

    if (form.style.display === "none") {
        form.style.display = "block";
    } else {
        form.style.display = "none";
    }
}

//creates event by sending form data to server to save to db, then reloads events list to show new event
async function handleCreateEvent() {
    let body = 
    "title=" + document.getElementById("title").value +
    "&description=" + document.getElementById("description").value +
    "&event_date=" + document.getElementById("event_date").value +
    "&event_time=" + document.getElementById("event_time").value +
    "&location=" + document.getElementById("location").value +
    "&chapter=" + document.getElementById("chapter").value;

    //sends form data to server to save to db
    await fetch("createEvent.php", {
        method: "POST",
        headers: {         
            "Content-Type": "application/x-www-form-urlencoded" 
        },
        body: body 
    });

    loadEvents();

    //clear form and hide it again
    document.getElementById("createEventForm").reset();
    toggleCreateForm();
}