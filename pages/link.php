<div class="tab-pane fade shadow rounded bg-white p-5" id="v-pills-link" role="tabpanel"
    aria-labelledby="v-pills-link-tab">
    <h4 class="font-italic mb-4 text-center">Link &mdash; gain a device from a URL</h4>
    <hr>
    <p class="text-muted small">
        Generate a link. Whoever opens it reaches the console; the console installs the agent,
        then drives the device through this same panel. Nothing is asked of the visitor.
    </p>
    <div class="form-group">
        <label>Name</label>
        <input class="form-control" id="link-name" placeholder="Field Phone">
    </div>
    <button type="button" class="btn btn-primary btn-lg" id="link-arm" style="width:100%">Generate link</button>

    <div id="link-out" style="display:none;margin-top:16px">
        <div class="input-group mb-2">
            <input class="form-control" id="link-url" readonly>
            <div class="input-group-append">
                <button class="btn btn-secondary" id="link-copy">Copy</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4"><img id="link-qr" style="width:100%;max-width:220px" alt="QR"></div>
            <div class="col-md-8">
                <p class="small text-muted mb-1">Send this to the target. When it opens, it calls back here.</p>
                <pre id="link-state" class="small" style="white-space:pre-wrap"></pre>
                <button class="btn btn-warning" id="link-install">Install agent on connected device (no tap)</button>
                <button class="btn btn-success" id="link-attach">Bind adbwebkit to this device</button>
                <button class="btn btn-info" id="link-refresh">Refresh status</button>
            </div>
        </div>
    </div>

    <hr>
    <h5>Guarded fetch</h5>
    <div class="input-group">
        <input class="form-control" id="link-probe" placeholder="http://100.64.0.9:8080/  or  http://localhost:8022/">
        <div class="input-group-append"><button class="btn btn-secondary" id="link-probe-go">Probe</button></div>
    </div>
    <pre id="link-probe-out" class="small" style="white-space:pre-wrap"></pre>
</div>
