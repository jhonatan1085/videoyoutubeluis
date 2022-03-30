<script>
    try {

        onScan.attachTo(document, {
            suffixKeyCodes: [13], // enter-key expected at the end of a scan
            //reactToPaste: true, // Compatibility to built-in scanners in paste-mode (as opposed to keyboard-mode)
            onScan: function(barcode) { // Alternative to document.addEventListener('scan')
                console.log(barcode);
                window.livewire.emit('scan-code', barcode)
            },
            onScanError: function(e) {
                console.log(e)
            },

            // onKeyDetect: function(iKeyCode) { // output all potentially relevant key events - great for debugging!
            //     console.log('Pressed: ' + iKeyCode);
            // }
        })

        console.log('el scaner esta listo')

    } catch (error) {
        console.log('Error de lectura', error);
    }
</script>
