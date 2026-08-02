<html>

<head>
    <style>
        .receipt {
            page-break-after: always;
            text-align: center;
        }

        img {
            max-width: 90%;
            max-height: 90vh;
        }
    </style>
</head>

<body>

    @foreach($receipts as $receipt)

    <div class="receipt">

        <h3>
            {{ $receipt->merchant }}
        </h3>

        <p>
            {{ $receipt->receipt_date }}
        </p>


        <img src="{{ Storage::disk('s3')
    ->temporaryUrl(
        $receipt->file_path,
        now()->addMinutes(10)
    )
}}">

    </div>

    @endforeach

</body>

</html>
