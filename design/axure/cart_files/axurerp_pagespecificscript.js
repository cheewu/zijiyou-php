
var PageName = 'cart';
var PageId = 'pf286e0dab1ee46418d912e5e871e96f4'
var PageUrl = 'cart.html'
document.title = 'cart';

if (top.location != self.location)
{
	if (parent.HandleMainFrameChanged) {
		parent.HandleMainFrameChanged();
	}
}

if (window.OnLoad) OnLoad();
