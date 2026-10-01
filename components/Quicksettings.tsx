import React, { useState } from 'react'
import { BsGearFill, BsPower } from 'react-icons/bs';
import PowerMenu from './PowerMenu';

const Quicksettings = () => {
  const [pm, setPm] = useState(false);

  return (
    <div className='h-1/2 w-full bg-gray-700 absolute top-[4.5%]'>
      <div className="h-8 w-full my-2 bg-ambder-500 flex px-5 items-center ">
        <div className="ml-auto cursor-pointer flex gap-3">
          <BsGearFill />
          <BsPower className='stroke-1' onClick={() => setPm(true)} />
        </div>

        <PowerMenu isOpen={pm} close={() => setPm(false)} />
      </div>
    </div>
  )
}

export default Quicksettings;