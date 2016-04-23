


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <title>Ashish's Blog</title>
    <script src="../../includes/jquery/js/jquery-1.6.2.min.js"></script>
        <script src="../../includes/jquery/js/jquery-ui-1.8.16.custom.min.js"></script>
        <script src="../../includes/bootbox/bootbox.js"></script>
        
        <script src="../../includes/toastmessage/src/main/javascript/jquery.toastmessage.js"></script>
        <link rel="stylesheet" type="text/css" href="../../includes/toastmessage/src/main/resources/css/jquery.toastmessage.css" />
    <script type="text/javascript">
 
        function showSuccessToast() {
            $().toastmessage('showSuccessToast', "Success Dialog which is fading away ...");
        }
        function showStickySuccessToast() {
            $().toastmessage('showToast', {
                text: 'Success Dialog which is sticky',
                sticky: true,
                position: 'middle-center',
                type: 'success',
                closeText: '',
                close: function () {
                    console.log("toast is closed ...");
                }
            });
 
        }
        function showNoticeToast() {
            $().toastmessage('showNoticeToast', "Notice  Dialog which is fading away ...");
        }
        function showStickyNoticeToast() {
            $().toastmessage('showToast', {
                text: 'Notice Dialog which is sticky',
                sticky: true,
                position: 'middle-center',
                type: 'notice',
                closeText: '',
                close: function () { console.log("toast is closed ..."); }
            });
        }
        function showWarningToast() {
            $().toastmessage('showWarningToast', "Warning Dialog which is fading away ...");
        }
        function showStickyWarningToast() {
            $().toastmessage('showToast', {
                text: 'Warning Dialog which is sticky',
                sticky: true,
                position: 'middle-center',
                type: 'warning',
                closeText: '',
                close: function () {
                    console.log("toast is closed ...");
                }
            });
        }
        function showErrorToast() {
            $().toastmessage('showErrorToast', "Error Dialog which is fading away ...");
        }
        function showStickyErrorToast() {
            $().toastmessage('showToast', {
                text: 'Error Dialog which is sticky',
                sticky: true,
                position: 'middle-center',
                type: 'error',
                closeText: '',
                close: function () {
                    console.log("toast is closed ...");
                }
            });
        }
  
</script>
</head>
<body>
    <form id="form1" runat="server">
    <div>
  
    <p>
            <span class="description">Show a success toast</span> <a href="javascript:showSuccessToast();">not
            sticky</a>|<a href="javascript:showStickySuccessToast();">sticky</a>
        </p>
  
        <p>
            <span class="description">Show a notice toast</span> <a href="javascript:showNoticeToast();">not sticky</a>|<a
                href="javascript:showStickyNoticeToast();">sticky</a>
        </p>
  
        <p>
            <span class="description">Show a warning toast</span> <a href="javascript:showWarningToast();">not
            sticky</a>|<a href="javascript:showStickyWarningToast();">sticky</a>
        </p>
  
        <p>
            <span class="description">Show a error toast</span> <a href="javascript:showErrorToast();">not sticky</a>|<a
                href="javascript:showStickyErrorToast();">sticky</a>
        </p>
        <button onclick="showStickySuccessToast()">a</button>
  
    </div>
    </form>
</body>
</html>