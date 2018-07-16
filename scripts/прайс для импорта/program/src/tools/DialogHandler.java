package tools;

import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.FileDialog;

import jdk.nashorn.tools.Shell;

public class DialogHandler {
	private int type=SWT.OPEN;
	private String PathToFile="";
	private String[] filterArray;
	public DialogHandler(String[] mask,String[] description,String StartPath,org.eclipse.swt.widgets.Shell shell,String[] out) {
		FileDialog dialog = new FileDialog(shell, SWT.OPEN);
	    dialog.setFilterNames(description);
	    dialog.setFilterExtensions(mask); // Windows
	                                    // wild
	                                    // cards
	    dialog.setFilterPath(StartPath); // Windows path
	    dialog.setFileName("fred.bat");
	    String str = dialog.open();
	    out[0] = str ;
	   // IDialogHandler.Getval(str);
	    System.out.println("Save to: "+str);
	    
	};

}
