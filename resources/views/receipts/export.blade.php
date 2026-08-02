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

    @foreach ($receipts as $receipt)
        <div class="receipt">
            <h3>
                {{ $receipt->merchant }}
            </h3>
            <p>
                {{ $receipt->receipt_date }}
            </p>
            @if ($receipt->image_src)
                <img src="{{ $receipt->image_src }}">
            @else
                <p><em>Image unavailable</em></p>
            @endif
        </div>
    @endforeach

</body>

</html>
