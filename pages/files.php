<div class="tab-pane fade shadow rounded bg-white p-5" id="v-pills-files" role="tabpanel"
    aria-labelledby="v-pills-files-tab">
    <h4 class="font-italic mb-4 text-center">Files</h4>
    <hr>
    <button type="button" class="btn btn-primary btn-lg but" id="copyall">CopyAll</button>
    <button type=" button" class="btn btn-primary btn-lg but" id="moveall">MoveAll</button>
    <button type="button" class="btn btn-primary btn-lg but" style="width:100%;" id="delall">DelAll</button>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    <div class="table-responsive">
                        <input onkeyup="backpath(this)" path="/" value="/" id='searchfile'>
                        <br>
                        <p id="backme">Back</p>
                        <table class="table user-list " id="folder-list">



                        </table>
                        <table class="table user-list " id="file-list">


                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>