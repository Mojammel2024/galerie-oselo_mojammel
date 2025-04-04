// Wait for the page to fully load before running the code
document.addEventListener("DOMContentLoaded", () => {
    console.log("Admin panel loaded");

    // Find all links in the navigation menu and loop through them
    document.querySelectorAll("nav ul li a").forEach(link => {
        link.addEventListener("mouseover", () => {
            link.style.color = "#f9c784";
        });
        // Change the link color back to white when the mouse moves away
        link.addEventListener("mouseout", () => {
            link.style.color = "white";
        });
    });
});