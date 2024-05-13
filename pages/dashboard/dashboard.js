let dashboardPath = document.getElementsByTagName("script")[document.getElementsByTagName("script").length - 1].src;
let dashboardDirectory = dashboardPath.replace(dashboardPath.split('/')[dashboardPath.split('/').length - 1],'');
let postGetterPath = dashboardDirectory + 'dashboard_posts.php';

let containerBox = document.getElementById(idList.main);
let selectBox = document.getElementById(idList.filter);
let baseContent = containerBox.innerHTML;

selectBox.addEventListener('change', () => FilterPosts());
FilterPosts();

// Runs everytime the value of the filter box is changed
function FilterPosts() {
    if (!selectBox.value.trim().localeCompare(privPostKey.trim())) {

        // Display only private posts
        let url = postGetterPath + '?user=' + userId +'&s=1';
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function () {
            containerBox.innerHTML = baseContent + this.responseText;
        }
        xhttp.open('GET', url, true);
        xhttp.send();
        return;
    }

    // Display all visible posts
    let url = postGetterPath + '?user=' + userId +'&s=0';
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function () {
        containerBox.innerHTML = baseContent + this.responseText;
    }
    xhttp.open('GET', url, true);
    xhttp.send();
}