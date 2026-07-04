{{--
    PIN Setup Section — Include this in your profile/edit.blade.php
    @include('admin.profile.partials.pin-setup')
--}}

<div class="card mt-4" style="border-radius:14px;border:1px solid #e2e8f0;box-shadow:0 1px 4px rgba(0,0,0,.06);overflow:hidden;">
    <div class="card-header" style="background:#f8fafc;padding:16px 22px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-lock" style="color:#fff;font-size:.85rem;"></i>
            </div>
            <div>
                <div style="font-size:.88rem;font-weight:700;color:#0f172a;">Quick-Access PIN Lock</div>
                <div style="font-size:.72rem;color:#64748b;">4-digit PIN for returning sessions</div>
            </div>
        </div>
        @if(auth()->user()->pin && auth()->user()->pin_enabled)
        <span style="background:#dcfce7;color:#15803d;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:99px;text-transform:uppercase;letter-spacing:.04em;">
            <i class="fas fa-check-circle mr-1"></i> Active
        </span>
        @else
        <span style="background:#f1f5f9;color:#64748b;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:99px;text-transform:uppercase;letter-spacing:.04em;">
            Not Set
        </span>
        @endif
    </div>

    <div class="card-body" style="padding:22px;">

        {{-- Info box --}}
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;font-size:.82rem;color:#1e40af;margin-bottom:22px;display:flex;gap:10px;">
            <i class="fas fa-circle-info" style="margin-top:2px;flex-shrink:0;"></i>
            <div>
                Once set, when you return to the app after closing your browser,
                you'll see a <strong>4-digit PIN screen</strong> instead of a full login.
                Your session stays active for up to <strong>90 days</strong>.
                Clearing browser cookies will require a full login again.
            </div>
        </div>

        {{-- Set / Update PIN form --}}
        <form method="POST" action="{{ route('profile.pin.setup') }}" id="pinSetupForm">
            @csrf
            <div style="font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.07em;margin-bottom:14px;">
                {{ auth()->user()->pin ? 'Change PIN' : 'Set PIN' }}
            </div>
            <div class="row">
                <div class="col-md-4 form-group">
                    <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                        Current Password *
                    </label>
                    <input type="password" name="current_password" class="form-control"
                           style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:.85rem;padding:9px 12px;"
                           placeholder="Verify your password" required>
                    @error('current_password')
                    <div style="font-size:.73rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                        New 4-Digit PIN *
                    </label>
                    <div style="position:relative;">
                        <input type="password" name="pin" id="pinField" maxlength="4"
                               inputmode="numeric" pattern="\d{4}" class="form-control"
                               style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:1.2rem;padding:9px 38px 9px 12px;letter-spacing:.25em;"
                               placeholder="• • • •" required>
                        <button type="button" onclick="toggleField('pinField', 'pinEye')"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                            <i class="fas fa-eye" id="pinEye"></i>
                        </button>
                    </div>
                    @error('pin')
                    <div style="font-size:.73rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 form-group">
                    <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                        Confirm PIN *
                    </label>
                    <div style="position:relative;">
                        <input type="password" name="pin_confirmation" id="pinConfField" maxlength="4"
                               inputmode="numeric" pattern="\d{4}" class="form-control"
                               style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:1.2rem;padding:9px 38px 9px 12px;letter-spacing:.25em;"
                               placeholder="• • • •" required>
                        <button type="button" onclick="toggleField('pinConfField', 'pinConfEye')"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                            <i class="fas fa-eye" id="pinConfEye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Live match indicator --}}
            <div id="pinMatchIndicator" style="font-size:.75rem;margin-bottom:14px;display:none;">
                <span id="pinMatchText"></span>
            </div>

            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <button type="submit" id="pinSubmitBtn"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:9px;padding:10px 22px;font-size:.85rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .2s;">
                    <i class="fas fa-save"></i>
                    {{ auth()->user()->pin ? 'Update PIN' : 'Set PIN' }}
                </button>
                <span style="font-size:.75rem;color:#94a3b8;">Only digits 0-9 allowed</span>
            </div>
        </form>

        {{-- Disable PIN (only if currently active) --}}
        @if(auth()->user()->pin && auth()->user()->pin_enabled)
        <hr style="margin:24px 0;border-color:#f1f5f9;">
        <div style="font-size:.72rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.07em;margin-bottom:14px;">
            Disable PIN Lock
        </div>
        <form method="POST" action="{{ route('profile.pin.disable') }}" onsubmit="return confirm('Remove PIN lock? You will need full login each time after closing browser.')">
            @csrf
            <div class="row">
                <div class="col-md-4 form-group">
                    <label style="font-size:.78rem;font-weight:700;color:#374151;display:block;margin-bottom:6px;">
                        Confirm with Password *
                    </label>
                    <input type="password" name="current_password" class="form-control"
                           style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:.85rem;padding:9px 12px;"
                           placeholder="Your account password" required>
                    @error('current_password')
                    <div style="font-size:.73rem;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <button type="submit"
                    style="background:#fef2f2;color:#dc2626;border:1.5px solid #fca5a5;border-radius:9px;padding:9px 20px;font-size:.82rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .2s;">
                <i class="fas fa-lock-open"></i> Disable PIN Lock
            </button>
        </form>
        @endif
    </div>
</div>

<script>
function toggleField(id, iconId) {
    const f = document.getElementById(id);
    const i = document.getElementById(iconId);
    f.type = f.type === 'password' ? 'text' : 'password';
    i.classList.toggle('fa-eye');
    i.classList.toggle('fa-eye-slash');
}

// Only allow numeric input in PIN fields
['pinField','pinConfField'].forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 4);
        checkPinMatch();
    });
});

function checkPinMatch() {
    const a = document.getElementById('pinField')?.value;
    const b = document.getElementById('pinConfField')?.value;
    const indicator = document.getElementById('pinMatchIndicator');
    const text      = document.getElementById('pinMatchText');
    const submitBtn = document.getElementById('pinSubmitBtn');

    if (!a || !b) { indicator.style.display = 'none'; return; }

    indicator.style.display = 'block';
    if (a === b && a.length === 4) {
        text.innerHTML = '<i class="fas fa-check-circle" style="color:#059669;"></i> <span style="color:#059669;font-weight:600;">PINs match</span>';
        submitBtn.disabled = false;
    } else if (a !== b && b.length > 0) {
        text.innerHTML = '<i class="fas fa-times-circle" style="color:#dc2626;"></i> <span style="color:#dc2626;">PINs do not match</span>';
        submitBtn.disabled = true;
    } else {
        indicator.style.display = 'none';
        submitBtn.disabled = false;
    }
}
</script>
