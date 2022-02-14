<div class="col-md-9">
    <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade shadow rounded bg-white show active p-5" id="v-pills-shell" role="tabpanel"
            aria-labelledby="v-pills-shell-tab">
            <h4 class="font-italic mb-4 text-center">Shell</h4>
            <hr>
            <input type="text" class="form-control input-shell" placeholder="Enter Command Like ls /sdcard"
                id="command-shell">

            <div class="form-group purple-border">
                <textarea class="form-control " readonly id="result-shell" rows="3"
                    style="resize: none;"></textarea><br>
                <img src="asset/img/1.gif" class="img-fluid" style="display:none" id="loading">
                <button type="button" class="btn btn-secondary btn-md btn-block" id="run-shell">RUN</button>
            </div>
        </div>
            <script>
            $(document).ready(function() {
                var url = window.location.hash
                var clear = url.split("#")
                $("#v-pills-" + clear[1] + "-tab").click()

                $("a").click(function() {
                    var href = $(this).attr('href');
                    var hash = href.split('-')
                    window.location.hash = hash[2]





                })
            })
        </script>
