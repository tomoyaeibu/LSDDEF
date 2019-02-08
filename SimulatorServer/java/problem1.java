import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.InputStreamReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.OutputStreamWriter;
import java.io.PrintWriter;
import java.io.IOException;

public class problem1
{
    public static void main( String[] args )
    {
    	//inputファイルの読み込み
	    String inputFileName = "input.txt";
	    File inputFile = new File(inputFileName);
	    String input="";
	    try {
	    	// 入力ストリームの生成
	    	FileInputStream fis = new FileInputStream(inputFile);
	    	InputStreamReader isr = new InputStreamReader(fis);
	    	BufferedReader br = new BufferedReader(isr);
	    	// テキストファイルからの読み込み
	    	input = br.readLine();
	      
	    	// 後始末
	    	br.close();
	    	// エラーがあった場合は、スタックトレースを出力
	   	} catch(Exception e) {
	    	e.printStackTrace();
	    }

    	//処理
    	int waitingTime = Integer.parseInt(input);
    	try {
	        Thread.sleep(waitingTime*10);
	    } catch (InterruptedException e) {
	        e.printStackTrace();
	    }

    	//outputファイルの出力
	    File newfile = new File("output.txt");
	    try{
	      newfile.createNewFile();
	    }catch(IOException e){
	      System.out.println(e);
	    }

    	String outputFileName = "output.txt";
    	File outputFile = new File(outputFileName);
    	try {
      		// 出力ストリームの生成
      		FileOutputStream fos = new FileOutputStream(outputFile);
      		OutputStreamWriter osw = new OutputStreamWriter(fos);
      		PrintWriter pw = new PrintWriter(osw);
      		// ファイルへの書き込み
      		pw.println(input+"0");
      		pw.println("This is a result of processing the input");
      
      		// 後始末
      		pw.close();
    		// エラーがあった場合は、スタックトレースを出力
    	} catch(Exception e) {
      		e.printStackTrace();
    	}
    }
}