<script>
    var listener = new window.keypress.Listener();

    listener.simple_combo("f9",function(){
        livewire.emit('saveSale');
    })

    listener.simple_combo("f8",function(){
        document.getElementById('cash').value = ''
        document.getElementById('cash').focus()
    })

    listener.simple_combo("f4",function(){
        var total = parseFloat(document.getElementById('hiddenTotal').value());

        if(total > 0){
            Confirm(0,'clearCart', '¿Seguro de eliminar el carrito?' )
        }else{
            noty('Agrega productos  a la venta')
        }
        
    })
</script>