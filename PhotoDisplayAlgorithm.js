public class PhotoDisplayAlgorithm
{
	double priority; //how high priority the photo is
	double size; //how big the photo will be displayed
	double ratio; //
	double final SCREENSIZE;
	double screenRepresentationalSize;
	
	
	public void updateRatio()
	{
		ratio = SCREENSIZE/screenRepresentationalSize;
	}
	
	public void updateSize()
	{
		double maybeSize = priority*ratio;
		if (maybeSize > MINTHRESH && maybeSize <= MAXTHRESH)
		{
			size = maybeSize;
		}
		else if (maybeSize > MAXTHRESH)
		{
			size = MAXTHRESH;
		}
		else
		{
			size = 0;
		}
	}
}