<div class="tab-pane fade shadow rounded bg-white p-5" id="v-pills-application" role="tabpanel"
    aria-labelledby="v-pills-application-application">
    <h4 class="font-italic mb-4 text-center">Application</h4>
    <hr>


    <button type="button" class="btn btn-primary btn-lg but" onclick="install_apk()">InstallApk</button>

    <button type="button" class="btn btn-primary btn-lg but" onclick="reset_all()">Reset
        All Permisstion</button>
    <div class="form-group">
        <label for="sel1">Select list:</label>
        <select class="form-control" id="selectapp">
            <option selected>-------------</option>
            <option value="pm list packages">all packages</option>
            <option value="pm list packages -e">enabled packages</option>
            <option value="pm list packages -s">system packages</option>
            <option value="pm list packages -3">third party packages</option>
            <option value="pm list packages -d">disabled packages</option>



        </select>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="main-box clearfix">
                <div class="table-responsive">
                    <br>

                    <table class="table user-list " id="application">


                    </table>

                </div>

            </div>
        </div>
    </div>
</div>