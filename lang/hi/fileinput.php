<?php
// | => &#124; (for 'ja' file)
return [
	'fileSingle' => 'फ़ाइल',
	'filePlural' => 'फ़ाइलें',
	'browseLabel' => 'ब्राउज़ &hellip;',
	'removeLabel' => 'हटाना',
	'removeTitle' => 'चयनित फ़ाइलें साफ़ करें',
	'cancelLabel' => 'रद्द करें',
	'cancelTitle' => 'जारी अपलोड को निरस्त करें',
	'uploadLabel' => 'डालना',
	'uploadTitle' => 'चयनित फ़ाइलें अपलोड करें',
	'msgNo' => 'नहीं',
	'msgNoFilesSelected' => 'कोई फ़ाइल नहीं चुनी गई',
	'msgCancelled' => 'रद्द',
	'msgPlaceholder' => 'चुनते हैं {files}...',
	'msgZoomModalHeading' => 'विस्तृत पूर्वावलोकन',
	'msgFileRequired' => 'अपलोड करने के लिए आपको एक फ़ाइल का चयन करना होगा।',
	'msgSizeTooSmall' => 'फ़ाइल "{name}" (<b>{size} KB</b>) बहुत छोटा है और इससे बड़ा होना चाहिए <b>{minSize} KB</b>।',
	'msgSizeTooLarge' => 'फ़ाइल "{name}" (<b>{size} KB</b>) अधिकतम अनुमत अपलोड आकार से अधिक है <b>{maxSize} KB</b>।',
	'msgFilesTooLess' => 'आपको कम से कम चयन करना होगा <b>{n}</b> {files} अपलोड करना।',
	'msgFilesTooMany' => 'अपलोड के लिए चुनी गई फाइलों की संख्या <b>({n})</b> की अधिकतम अनुमत सीमा से अधिक है <b>{m}</b>।',
	'msgFileNotFound' => 'फ़ाइल "{name}" पता नहीं चला!',
	'msgFileSecured' => 'सुरक्षा प्रतिबंध फ़ाइल को पढ़ने से रोकते हैं "{name}"।',
	'msgFileNotReadable' => 'फ़ाइल "{name}" पठनीय नहीं है।',
	'msgFilePreviewAborted' => 'फ़ाइल पूर्वावलोकन के लिए निरस्त किया गया "{name}"।',
	'msgFilePreviewError' => 'फ़ाइल पढ़ते समय एक त्रुटि हुई "{name}"।',
	'msgInvalidFileName' => 'फ़ाइल नाम में अमान्य या असमर्थित वर्ण "{name}"।',
	'msgInvalidFileType' => 'फ़ाइल के लिए अमान्य प्रकार "{name}"। केवल "{types}" फ़ाइलें समर्थित हैं।',
	'msgInvalidFileExtension' => 'फ़ाइल के लिए अमान्य एक्सटेंशन "{name}"। केवल "{extensions}" फ़ाइलें समर्थित हैं।',
	'msgFileTypes' => [
		'image' => 'छवि',
		'html' => 'HTML',
		'text' => 'मूलपाठ',
		'video' => 'वीडियो',
		'audio' => 'ऑडियो',
		'flash' => 'flash',
		'pdf' => 'PDF',
		'object' => 'object'
	],
	'msgUploadAborted' => 'फ़ाइल अपलोड निरस्त कर दिया गया था',
	'msgUploadThreshold' => 'संसाधित किया जा रहा है...',
	'msgUploadBegin' => 'प्रारंभ किया जा रहा है...',
	'msgUploadEnd' => 'किया हुआ',
	'msgUploadEmpty' => 'अपलोड के लिए कोई मान्य डेटा उपलब्ध नहीं है।',
	'msgUploadError' => 'त्रुटि',
	'msgValidationError' => 'मान्यता त्रुटि',
	'msgLoading' => 'फ़ाइल लोड हो रही है {index} का {files} &hellip;',
	'msgProgress' => 'फ़ाइल लोड हो रही है {index} का {files} - {name} - {percent}% पूरा हुआ।',
	'msgSelected' => '{n} {files} गिने चुने',
	'msgFoldersNotAllowed' => 'केवल फ़ाइलें खींचें और छोड़ें! {n} गिराए गए फ़ोल्डर (फ़ोल्डर) को छोड़ दिया गया।',
	'msgImageWidthSmall' => 'छवि फ़ाइल की चौड़ाई "{name}" कम से कम होना चाहिए {size} px।',
	'msgImageHeightSmall' => 'छवि फ़ाइल की ऊंचाई "{name}" कम से कम होना चाहिए {size} px।',
	'msgImageWidthLarge' => 'छवि फ़ाइल की चौड़ाई "{name}" से अधिक नहीं हो सकता {size} px।',
	'msgImageHeightLarge' => 'छवि फ़ाइल की ऊंचाई "{name}" से अधिक नहीं हो सकता {size} px।',
	'msgImageResizeError' => 'आकार बदलने के लिए छवि आयाम नहीं मिल सके।',
	'msgImageResizeException' => 'छवि का आकार बदलने में त्रुटि।<pre>{errors}</pre>',
	'msgAjaxError' => 'के साथ कुछ गलत हुआ {operation} कार्यवाही। बाद में पुन: प्रयास करें!',
	'msgAjaxProgressError' => '{operation} अनुत्तीर्ण होना',
	'ajaxOperations' => [
		'deleteThumb' => 'फ़ाइल हटाएं',
		'uploadThumb' => 'फाइल अपलोड',
		'uploadBatch' => 'बैच फ़ाइल अपलोड',
		'uploadExtra' => 'फॉर्म डेटा अपलोड'
	],
	'dropZoneTitle' => 'फ़ाइलें यहां खींचें और छोड़ें &hellip;',
	'dropZoneClickTitle' => '<br>(या चयन करने के लिए क्लिक करें {files})',
	'fileActionSettings' => [
		'removeTitle' => 'फ़ाइल को हटाएं',
		'uploadTitle' => 'फ़ाइल अपलोड करें',
		'uploadRetryTitle' => 'अपलोड करने का पुनः प्रयास करें',
		'downloadTitle' => 'फ़ाइल डाउनलोड करें',
		'zoomTitle' => 'विवरण देखें',
		'dragTitle' => 'ले जाएँ / पुनर्व्यवस्थित करें',
		'indicatorNewTitle' => 'अभी तक अपलोड नहीं किया गया',
		'indicatorSuccessTitle' => 'अपलोड किए गए',
		'indicatorErrorTitle' => 'अपलोड त्रुटि',
		'indicatorLoadingTitle' => 'अपलोड हो रहा है...'
	],
	'previewZoomButtonTitles' => [
		'prev' => 'पिछली फ़ाइल देखें',
		'next' => 'अगली फ़ाइल देखें',
		'toggleheader' => 'शीर्षलेख टॉगल करें',
		'fullscreen' => 'पूर्णस्क्रीन चालू करें',
		'borderless' => 'सीमा रहित मोड टॉगल करें',
		'close' => 'विस्तृत पूर्वावलोकन बंद करें'
	]
];
