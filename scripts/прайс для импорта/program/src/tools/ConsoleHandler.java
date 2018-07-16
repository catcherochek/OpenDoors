package tools;

import org.eclipse.swt.SWT;
import org.eclipse.wb.swt.SWTResourceManager;

import org.eclipse.swt.widgets.Text;

public class ConsoleHandler {
	public static final int CONSOLEHANDLER_LOGTYPE_MESSAGE = 1;
	public static final int CONSOLEHANDLER_LOGTYPE_WARNING = 2;
	public static final int CONSOLEHANDLER_LOGTYPE_ERROR = 3;
	private Text txtConsole;
	
	
	public ConsoleHandler(Text txts){
		txtConsole = txts;
		txtConsole.setEditable(false);
	}
	public void Log(String text,int Log_type) {
		switch(Log_type) {
		case CONSOLEHANDLER_LOGTYPE_MESSAGE:{
			txtConsole.setForeground(SWTResourceManager.getColor(SWT.COLOR_WHITE));
			txtConsole.append("\n MESSAGE:"+text);
		}
		case CONSOLEHANDLER_LOGTYPE_WARNING:{
			txtConsole.setForeground(SWTResourceManager.getColor(SWT.COLOR_YELLOW));
			txtConsole.append("\n WARNING:"+text);
			
		}
		}
	}
}
