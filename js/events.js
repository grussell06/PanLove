//wait until html fully loads so it can find eventscontainer
document.addEventListener("DOMContentLoaded", function() {
    loadEvents();
});


//get events from db and display them on page
async function loadEvents() {
    try{ 
        let response = await fetch("getEvents.php");
        let data = await response.json();

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

                    <button class="rsvp-btn" data-event-id="${event.event_id}">RSVP</button>
                    <span class="rsvp-count">${event.rsvp_count || 0} going</span>
                    </div>
            `;
        });
    } catch (err) {
        console.error("Error loading events:", err);
    }
}


//send rsvp to server and save to db 
/*
async function rsvp(eventId) {
    try {
        let response = await fetch("rsvp.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "event_id=" + eventId
        });

        let result = await response.json();
        let countSpan = document.getElementById(`count-${eventId}`);

        if (result.status === "added" || result.status === "removed") {
            countSpan.innerText = result.count + " going";
        }
        else if (result.status === "not_logged_in") {
            alert("Please log in first");
        }
        else {
            alert("Server error");
        }
    } catch (err) {
        console.error("RSVP error:", err);
    }
}
    */

//rsvp button to look like like button 
document.addEventListener("click", function(e) {
    if (e.target.classList.contains("rsvp-btn")) {
        const button = e.target;
        const eventId = button.getAttribute("data-event-id");
        const countSpan = button.nextElementSibling; // The span with the count

        fetch("rsvp.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "event_id=" + eventId
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "added" || data.status === "removed") {
                // Update the count text
                countSpan.innerText = data.count + " going";
                
                // Optional: Change button style based on status
                if (data.status === "added") {
                    button.classList.replace("btn-outline-primary", "btn-primary");
                    button.innerText = "Going!";
                } else {
                    button.classList.replace("btn-primary", "btn-outline-primary");
                    button.innerText = "RSVP";
                }
            } else if (data.status === "not_logged_in") {
                alert("Please log in first");
            } else {
                alert("An error occurred.");
            }
        })
        .catch(err => console.error("RSVP Error:", err));
    }
});

//this shows the form when user clicks create event, otherwise is hidden 
function toggleCreateForm() {
    let form = document.getElementById("createEventForm");
    form.style.display = (form.style.display === "none") ? "block" : "none";
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