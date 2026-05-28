{{-- resources/views/user/verify_wallet.blade.php --}}

@extends('layouts.master')

@section('title')
    Wallet Verification
@endsection

@section('content')

<section class="content-header">
    <h1>
        <i class="fa fa-shield"></i>
        Wallet Verification
    </h1>
</section>

<section class="content">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <div class="box verification-card">

                <div class="box-body">

                    <div class="text-center verification-header">

                        <div class="verification-icon">
                            <i class="fa fa-wallet"></i>
                        </div>

                        <h2>Verify Wallet</h2>

                        <p class="text-muted">
                            Enter a wallet ID to verify before saving.
                        </p>

                    </div>

                    {{-- IF WALLET ALREADY EXISTS --}}
                    @if(!empty($office->withinhere_wallet_id))

                        <div class="alert alert-success already-linked-box">

                            <div class="linked-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>

                            <h3>
                                Wallet Already Linked
                            </h3>

                            <p class="text-muted">
                                This office already has a linked wallet.
                            </p>

                            <div class="linked-wallet-id">
                                {{ $office->withinhere_wallet_id }}
                            </div>

                        </div>

                    @else

                    {{-- WALLET FORM --}}

                    <div class="form-group">

                        <label>Wallet ID</label>

                        <div class="input-group input-group-lg">

                            <span class="input-group-addon">
                                <i class="fa fa-id-card"></i>
                            </span>

                            <input
                                type="text"
                                class="form-control"
                                id="wallet_id"
                                placeholder="Example: WH-195844"
                            >

                        </div>

                    </div>

                    <button
                        type="button"
                        class="btn btn-primary btn-lg btn-block"
                        id="verifyBtn"
                    >
                        <i class="fa fa-search"></i>
                        Verify Wallet
                    </button>

                    {{-- Loading --}}
                    <div
                        id="loadingState"
                        class="text-center"
                        style="display:none; margin-top:20px;"
                    >
                        <i class="fa fa-spinner fa-spin fa-2x text-primary"></i>

                        <p class="text-muted" style="margin-top:10px;">
                            Verifying wallet...
                        </p>
                    </div>

                    {{-- Success Result --}}
                    <div
                        id="walletResult"
                        style="display:none; margin-top:25px;"
                    >

                        <div class="verified-card">

                            <div class="verified-top">
                                <i class="fa fa-check-circle"></i>
                                Wallet Verified Successfully
                            </div>

                            <div class="verified-details">

                                <div class="detail-item">

                                    <span class="detail-label">
                                        Full Name
                                    </span>

                                    <div
                                        class="detail-value"
                                        id="walletFullName"
                                    ></div>

                                </div>

                                <div class="detail-item">

                                    <span class="detail-label">
                                        User ID
                                    </span>

                                    <div
                                        class="detail-value"
                                        id="walletUserId"
                                    ></div>

                                </div>

                            </div>

                            <form
                                method="POST"
                                action="{{ url('user/save_wallet') }}"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="wallet_id"
                                    id="save_wallet_id"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-success btn-lg btn-block"
                                >
                                    <i class="fa fa-save"></i>
                                    Save Wallet
                                </button>

                            </form>

                        </div>

                    </div>

                    {{-- Error --}}
                    <div
                        id="walletError"
                        style="display:none; margin-top:20px;"
                    >

                        <div class="alert alert-danger error-box">

                            <i class="fa fa-times-circle"></i>

                            <span id="walletErrorText"></span>

                        </div>

                    </div>

                    @endif

                </div>

            </div>

        </div>
    </div>

</section>

@endsection

@section('footer-scripts')

<style>

.verification-card{
    border-radius:12px;
    overflow:hidden;
    border:none;
    box-shadow:0 4px 18px rgba(0,0,0,0.08);
}

.verification-header{
    margin-bottom:30px;
}

.verification-icon{
    width:90px;
    height:90px;
    margin:0 auto 15px;
    border-radius:50%;
    background:linear-gradient(135deg,#3c8dbc,#00c0ef);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:38px;
    box-shadow:0 4px 15px rgba(60,141,188,0.3);
}

.verification-header h2{
    font-weight:700;
    margin-top:10px;
    margin-bottom:8px;
}

.input-group-lg>.form-control{
    height:50px;
    font-size:16px;
}

.input-group-addon{
    background:#f7f7f7;
}

#verifyBtn{
    border-radius:8px;
    padding:14px;
    font-size:16px;
    font-weight:600;
}

.verified-card{
    background:#f8fffb;
    border:1px solid #d9f5e4;
    border-radius:12px;
    padding:20px;
}

.verified-top{
    background:#00a65a;
    color:#fff;
    padding:14px;
    border-radius:8px;
    font-size:16px;
    font-weight:600;
    text-align:center;
    margin-bottom:20px;
}

.verified-top i{
    margin-right:6px;
}

.detail-item{
    background:#fff;
    border:1px solid #eee;
    border-radius:8px;
    padding:15px;
    margin-bottom:12px;
}

.detail-label{
    display:block;
    font-size:12px;
    color:#888;
    text-transform:uppercase;
    font-weight:600;
    margin-bottom:5px;
}

.detail-value{
    font-size:18px;
    font-weight:700;
    color:#333;
}

.error-box{
    border-radius:8px;
    font-size:15px;
}

.error-box i{
    margin-right:6px;
}

/* Already linked wallet */
.already-linked-box{
    text-align:center;
    padding:35px 25px;
    border-radius:12px;
    border:none;
    background:#f8fffb;
}

.linked-icon{
    font-size:60px;
    color:#00a65a;
    margin-bottom:15px;
}

.already-linked-box h3{
    margin-top:0;
    font-weight:700;
    color:#00a65a;
}

.linked-wallet-id{
    margin-top:20px;
    background:#fff;
    border:1px solid #d9f5e4;
    padding:15px;
    border-radius:10px;
    font-size:20px;
    font-weight:700;
    color:#333;
    letter-spacing:1px;
}

</style>

<script>

$(document).ready(function () {

    $('#verifyBtn').click(function () {

        let walletId = $('#wallet_id').val();

        $('#walletResult').hide();
        $('#walletError').hide();

        if(walletId.trim() === ''){

            $('#walletError').show();

            $('#walletErrorText').text('Please enter a wallet ID');

            return;
        }

        $('#loadingState').show();

        $.ajax({

            url: "{{ url('user/wallet_verification') }}",

            type: "POST",

            data: {
                _token: "{{ csrf_token() }}",
                wallet_id: walletId
            },

            success: function (response) {

                console.log(response);

                $('#loadingState').hide();

                $('#walletResult').fadeIn();

                $('#walletFullName').html(response.fullName);

                $('#walletUserId').html(response.userId);

                $('#save_wallet_id').val(walletId);

            },

            error: function (xhr) {

                $('#loadingState').hide();

                $('#walletResult').hide();

                $('#walletError').fadeIn();

                if (xhr.responseJSON && xhr.responseJSON.message) {

                    $('#walletErrorText').text(xhr.responseJSON.message);

                } else {

                    $('#walletErrorText').text('Wallet verification failed');
                }
            }

        });

    });

});

</script>

@endsection