/**
* SWFUpload
* @author Lars Huring and Olov Nilzén
* @version 1.0.1 (2007-03-15 17:40)
*/

// Import packages
import flash.net.FileReferenceList;
import flash.net.FileReference;
import flash.external.ExternalInterface;
import com.mammon.swfupload.Delegate;


class com.mammon.swfupload.SWFUpload
{

	// Private variables
	private var controlId = Math.floor(Math.random() * 10000) + 1;
	private var currentFile:FileReference = null;
	private var currentFileId:Number = 0;
	private var fileList:FileReferenceList = new FileReferenceList();
	private var fileQueue:Array = new Array();
	private var intervalId = null;
	private var listener:Object = new Object();
	private var fileQueueLength:Number = 0;
	private var position:Number = 0;

	// Public variables
	private var allowedFiletypes:String;
	private var allowedFiletypesDescription:String;
	private var autoUpload:Boolean = false;
	private var maximumFilesize:Number;
	private var uploadScript:String;
	private var uploadFileCancelCallback:String;
	private var uploadDialogCancelCallback:String;
	private var uploadFileCompleteCallback:String;
	private var uploadFileErrorCallback:String;
	private var uploadFileQueuedCallback:String;
	private var uploadProgressCallback:String;
	private var uploadQueueCompleteCallback:String;
	private var uploadFileStartCallback:String;
	private var flashLoadedCallback:String;
	
	static function main() 
	{
		var SWFUpload:SWFUpload = new SWFUpload();
	}
	
	/**
	* Initialize class.
	*
	* @return {Void}
	*/
	function SWFUpload()
	{
		
		// Get flashvars
		allowedFiletypes = _root.allowedFiletypes;
		allowedFiletypesDescription = _root.allowedFiletypesDescription;
		autoUpload = _root.autoUpload;
		maximumFilesize = _root.maximumFilesize;
		uploadScript = _root.uploadScript;
		uploadFileCancelCallback = _root.uploadFileCancelCallback;
		uploadDialogCancelCallback = _root.uploadDialogCancelCallback;
		uploadFileCompleteCallback = _root.uploadFileCompleteCallback;
		uploadFileErrorCallback = _root.uploadFileErrorCallback;
		uploadFileQueuedCallback = _root.uploadFileQueuedCallback;
		uploadProgressCallback = _root.uploadProgressCallback;
		uploadQueueCompleteCallback = _root.uploadQueueCompleteCallback;
		flashLoadedCallback = _root.flashLoadedCallback;
		uploadFileStartCallback = _root.uploadFileStartCallback;
				
		// Security fix
		System.security.allowDomain("*");

		// set fileList events
		listener.onCancel = Delegate.create(this, uploadCancel);
		listener.onComplete = Delegate.create(this, uploadComplete);
		listener.onProgress = Delegate.create(this, uploadProgress);
		listener.onHTTPError = Delegate.create(this, uploadHTTPError);
		listener.onIOError = Delegate.create(this, uploadIOError);
		listener.onSecurityError = Delegate.create(this, uploadSecurityError);
		listener.onSelect = Delegate.create(this, uploadSelect);

		// Add listener to the fileList object
		fileList.addListener(listener);

		// Expose external functions
		ExternalInterface.addCallback("browse", this, browse);
		ExternalInterface.addCallback("upload", this, upload);
		ExternalInterface.addCallback("cancelFile", this, cancelFile);
		ExternalInterface.addCallback("cancelQueue", this, cancelQueue);
		
		if (flashLoadedCallback.length > 0)
			ExternalInterface.call(flashLoadedCallback, true);
	}

	/**
	* Open the file-browsing dialog box with allowed filetypes.
	*
	* @return {Void}
	*/
	private function browse():Void 
	{
		fileList.browse([{description: allowedFiletypesDescription, extension: allowedFiletypes}]);
	}

	/**
	* Cancel the file upload with specified id.
	* If no id or invalid, cancel current upload.
	*
	* @param {String} id Id for the file.
	* @return {Void}
	*/
	public function cancelFile(id:String):Void 
	{
		
		// Get file reference for id file if exists else for current file
		// var fileId:Number = (FileReference(id.split("_", 2)[1])) ? id.split("_", 2)[1] : currentFileId;
		var fileId = id.split("_", 2)[1];
		var file:FileReference = FileReference(fileQueue[fileId]);
		
		if (file) 
		{

			// Cancel upload
			file.cancel();
			
			delete fileQueue[fileId];
			fileQueueLength--;
			
			// Call home E.T.
			if (uploadFileCancelCallback.length > 0)
				ExternalInterface.call(uploadFileCancelCallback, getFileObject(fileId, file), fileQueueLength);

		};
			
	}

	/**
	* Cancel the whole upload queue.
	*
	* @return {Void}
	*/
	private function cancelQueue():Void 
	{
		
		var file:FileReference = null;

		// Loop from current position in queue
		for (var i = currentFileId; i < fileQueue.length; i++) 
		{

			// Get file pointer
			file = FileReference(fileQueue[i]);

			// Check file pointer
			if (file) 
			{

				// Cancel upload
				file.cancel();

				// Delete from queue
				delete fileQueue[i];
				fileQueueLength--;

				// Call home E.T.
				if (uploadFileCancelCallback.length > 0)
					ExternalInterface.call(uploadFileCancelCallback, getFileObject(i, file), fileQueueLength);

			}
		}
		
		fileQueueLength = 0;

		if(uploadQueueCompleteCallback.length > 0)
			ExternalInterface.call(uploadQueueCompleteCallback);
	}

	/**
	* Start the upload.
	*
	* @return {Void}
	*/
	private function upload():Void {
		
		var currentFile:FileReference = null;
		
		// Get the next file to upload
		while (!currentFile && fileQueue.length - 1 >= currentFileId) 
		{

			// Get file
			currentFile = FileReference(fileQueue[currentFileId]);
			
			if (!currentFile) 
			{
				delete fileQueue[currentFileId];
				currentFileId++;
			}
		} 

		if (currentFile) {
			
			position++;
			
			// Call home E.T. - file obj, file count & file queue length
			if(uploadFileStartCallback.length > 0)
				ExternalInterface.call(uploadFileStartCallback, getFileObject(currentFileId, currentFile), position, fileQueueLength);

			// Add listener
			currentFile.addListener(listener);
			
			// Start upload
			currentFile.upload(uploadScript);
			
		}
		else
		{
			// Call home E.T.
			if(uploadQueueCompleteCallback.length > 0)
				ExternalInterface.call(uploadQueueCompleteCallback);
		}
	}


	/**
	* @method uploadCancel
	* @description Invoked when the user dismisses the file-browsing dialog box.
	* @return {Void}
	*/
	private function uploadCancel():Void 
	{
		if (uploadDialogCancelCallback.length > 0)
			ExternalInterface.call(uploadDialogCancelCallback);
	};

	/**
	* Invoked when the upload operation has successfully completed.
	*
	* @param {FileReference} file The FileReference object that initiated the operation.
	* @return {Void}
	*/
	private function uploadComplete(file:FileReference):Void 
	{
		if (uploadFileCompleteCallback.length > 0)
			ExternalInterface.call(uploadFileCompleteCallback, getFileObject(currentFileId, file));
		
		fileComplete();
	};

	/**
	* Invoked when an upload fails because of an HTTP error
	*
	* @param {FileReference} file The File Reference object that initiated the operation.
	* @param {Number} httpError The HTTP error that caused this upload to fail.
	* @return {Void}
	*/
	private function uploadHTTPError(file:FileReference, httpError:Number):Void 
	{
		ExternalInterface.call(uploadFileErrorCallback, -10, getFileObject(currentFileId, file), httpError);
		fileComplete();
	};

	/**
	* Invoked when an input error occurs.
	*
	* @param {FileReference} file The FileReference object that initiated the operation.
	* @return {Void}
	*/
	private function uploadIOError(file:FileReference):Void 
	{
		if(uploadScript.length > 0)
			ExternalInterface.call(uploadFileErrorCallback, -30, getFileObject(currentFileId, file));
		else
			ExternalInterface.call(uploadFileErrorCallback, -20, getFileObject(currentFileId, file), "No backend file specified");

		fileComplete();
	};

	/**
	* @method uploadProgress
	* @description Invoked periodically during the file upload.
	* @param {FileReference} file The FileReference object that initiated the operation.
	* @param {Number} bytesLoaded The number of bytes transmitted so far.
	* @param {Number} bytesTotal The total size of the file to be transmitted, in bytes.
	* @return {Void}
	*/
	private function uploadProgress(file:FileReference, bytesLoaded:Number, bytesTotal:Number):Void 
	{
		if (uploadProgressCallback.length > 0)
			ExternalInterface.call(uploadProgressCallback, getFileObject(currentFileId, file), bytesLoaded, bytesTotal);
	};

	/**
	* Invoked when an upload fails because of a security error.
	*
	* @param {FileReference} file The FileReference object that initiated the operation.
	* @param {String} errorString Describes the error that caused onSecurityError to be called.
	* @return {Void}
	*/
	private function uploadSecurityError(file:FileReference, errorString:String):Void 
	{
		ExternalInterface.call(uploadFileErrorCallback, -40, getFileObject(currentFileId, file));
		fileComplete();
	};

	/**
	* Invoked when the user selects a file to upload from the file-browsing dialog box.
	*
	* @param {FileReference} file Reference to the file uploading.
	* @return {Void}
	*/
	public function uploadSelect(fileRefList:FileReferenceList):Void 
	{

		var fileListArray:Array = fileRefList.fileList;
		var file:FileReference;
		
		// Loop through all files in fileList
		for (var i:Number = 0; i < fileListArray.length; i++) 
		{
			// Get current file
			file = fileListArray[i];
			
			// Check filesize
			if(checkFileSize(file)) 
			{
				
				// Add file to queue
				fileQueue.push(file);
				fileQueueLength++;

				// If there is a callback - do it.
				if (uploadFileQueuedCallback.length > 0)
					ExternalInterface.call(uploadFileQueuedCallback, getFileObject(fileQueue.length - 1, file), fileQueueLength);
				
			} 
			else 
			{
				ExternalInterface.call(uploadFileErrorCallback, -50, getFileObject(fileQueue.length-1, file), "Filesize exceeds allowed limit.");
			};
			
		};
		
		// If autoUpload is enabled, start upload
		if (autoUpload == "true")
			upload();
	};

	/**
	* Invoked when the file is uploaded/canceled.
	*
	* @return {Void}
	*/
	private function fileComplete():Void 
	{
		
		// Delete file from queue
		delete fileQueue[currentFileId];
		
		// Increase current file pointer
		currentFileId++;

		// Check for more files
		upload();
	};

	/**
	* Returns an object from file.
	*
	* @param {Number} id The files id.
	* @param {FileReference} file Reference to the file.
	* @return {Void}
	*/
	private function getFileObject(id:Number, file:FileReference):Object 
	{
		return {id: controlId.toString() + "_" + id.toString(), name: file.name, size: file.size, type: file.type, creationDate: file.creationDate, creator: file.creator};
	};

	/**
	* Checks if the filesize is smaller than the maximum allowed filesize.
	*
	* @param {FileReference} file Reference to the file.
	* @return {Boolean}
	*/
	public function checkFileSize(file:FileReference):Boolean 
	{
		return (maximumFilesize > 0) ? file.size < (maximumFilesize * 1024) : true;
	};
}