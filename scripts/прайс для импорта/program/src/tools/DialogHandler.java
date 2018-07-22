package tools;

import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.FileDialog;

import jdk.nashorn.tools.Shell;

public class DialogHandler {
	private String out;
	public String getOut() {
		return out;
	}
	//private String PathToFile="";
	//private String[] filterArray;
	public DialogHandler(String[] mask,String[] description,String StartPath,org.eclipse.swt.widgets.Shell shell) {
		FileDialog dialog = new FileDialog(shell, SWT.OPEN);
	    dialog.setFilterNames(description);
	    dialog.setFilterExtensions(mask); // Windows
	                                    // wild
	                                    // cards
	    dialog.setFilterPath(StartPath); // Windows path
	    dialog.setFileName("fred.bat");
	    this.out = dialog.open();
	    
	   // IDialogHandler.Getval(str);
	    //System.out.println("Save to: "+str);
	    
	};

}
