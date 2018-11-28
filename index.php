<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Infinite Scroll</title>
    <style>
      #blog-posts {
        width: 700px;
		
      }
      .blog-post {
        border: 1px solid black;
        margin: 10px 10px 20px 10px;
        padding: 6px 10px;
      }
      #spinner {
        display: none;
      }
    </style>
  </head>
  <body>
    <div id="blog-posts">

    </div>

    <div id="spinner">
      <img src="spinner.gif" width="50" height="50" />
    </div>

    <div id="load-more-container">
      <button id="load-more" data-page="0">Load more</button>
    </div>

    <script language="javascript">

      var container = document.getElementById('blog-posts');
      var load_more = document.getElementById('load-more');
      let request_in_progress = false;

      function showSpinner() {
        var spinner = document.getElementById("spinner");
        spinner.style.display = 'block';
      }

      function hideSpinner() {
        var spinner = document.getElementById("spinner");
        spinner.style.display = 'none';
      }

      function showLoadMore() {
        load_more.style.display = 'inline';
      }

      function hideLoadMore() {
        load_more.style.display = 'none';
      }

      function appendToDiv(div, new_html) {
        // Put the new_html in to a temp div - causes browser to parse elements
        let temp = document.createElement('div');
        temp.innerHTML = new_html;

        // Use firstElementChild b/c of how DOM treats whitespace
        let class_name = temp.firstElementChild.className;
        let items = temp.getElementsByClassName(class_name);

        let len = items.length;
        for (i = 0; i < len; i++) {
          div.appendChild(items[0]);
        }


      }


      function setCurrentPage(page) {
        console.log('Page++  to ' + page);
      load_more.setAttribute('data-page', page);
      }

      function loadMore() {
        if(request_in_progress) { return;}
        request_in_progress = true;
        showSpinner();
        hideLoadMore();

        let page = parseInt(load_more.getAttribute('data-page'));
        let next_page =  page + 1;
        fetch("blog_posts.php?page=" + next_page,
          {
          method:"GET",
          headers: {
            // Not to use FormData with content type, used for gatherFormData
            // "Content-type":"application/x-www-form-urlencoded",
            "X-REQUESTED-WITH" : "XMLHttpRequest"
          },
          credentials: 'same-origin' })
        .then(response => response.text() )
      .then(data => {
        hideSpinner();
        setCurrentPage(next_page);
        showLoadMore();
        request_in_progress = false;
        appendToDiv(container, data);
          // console.log('Result ' + data);
      });


      }

      load_more.addEventListener("click", loadMore);

      function scrollReaction() {
    let current_height = container.offsetHeight;
    let current_y = window.innerHeight + window.pageYOffset;
    console.log(current_y + ' / ' + current_height);
    if(current_y >= current_height) {
      loadMore();
    }
      }


      window.onscroll = () => {
        scrollReaction();
      }

      // Load even the first page with Ajax
      loadMore();
    </script>

  </body>
</html>
